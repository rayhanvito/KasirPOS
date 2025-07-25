<template>
    <a-modal
        :open="visible"
        :centered="true"
        :maskClosable="false"
        :title="$t('common.print_invoice')"
        width="400px"
        @cancel="onClose"
    >
        <div id="pos-invoice">
            <div style="max-width: 400px; margin: 0px auto" v-if="order && order.xid">
                <div class="invoice-header">
                    <img
                        class="invoice-logo"
                        :src="selectedWarehouse.logo_url"
                        :alt="selectedWarehouse.name"
                    />
                </div>
                <div class="company-details">
                    <h2>{{ selectedWarehouse.name }}</h2>
                    <p class="company-address">
                        {{ selectedWarehouse.address }}
                    </p>
                    <h4 style="margin-bottom: 0px">
                        {{ $t("common.phone") }}: {{ selectedWarehouse.phone }}
                    </h4>
                    <h4>{{ $t("common.email") }}: {{ selectedWarehouse.email }}</h4>
                </div>
                <div class="tax-invoice-details">
                    <h3 class="tax-invoice-title">{{ $t("sales.tax_invoice") }}</h3>
                    <table class="invoice-customer-details">
                        <tr>
                            <td style="width: 50%">
                                {{ $t("sales.invoice") }} &nbsp;&nbsp;&nbsp;&nbsp;:
                                {{ order.invoice_number }}
                            </td>
                            <td style="width: 50%">
                                {{ $t("common.date") }} :
                                {{ formatDate(order.order_date) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%">
                                {{ $t("stock.customer") }} : {{ order.user.name }}
                            </td>
                            <td style="width: 50%">
                                {{ $t("stock.sold_by") }} : {{ order.staff_member.name }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="tax-invoice-items">
                    <table style="width: 100%">
                        <thead style="background: #eee">
                            <td style="width: 5%">#</td>
                            <td style="width: 25%">{{ $t("common.item") }}</td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '10%'
                                        : '25%',
                                }"
                            >
                                {{ $t("common.qty") }}
                            </td>
                            <td
                                v-if="selectedWarehouse.show_mrp_on_invoice"
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '20%',
                                }"
                            >
                                {{ $t("product.mrp") }}
                            </td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '25%',
                                }"
                            >
                                {{ $t("common.rate") }}
                            </td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '25%',
                                    textAlign: 'right',
                                }"
                            >
                                {{ $t("common.total") }}
                            </td>
                        </thead>
                        <tbody>
                            <tr
                                class="item-row"
                                v-for="(item, index) in order.items"
                                :key="item.xid"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.product.name }}</td>
                                <td>{{ item.quantity + "" + item.unit.short_name }}</td>
                                <td v-if="selectedWarehouse.show_mrp_on_invoice">
                                    {{ item.mrp ? formatAmountCurrency(item.mrp) : "-" }}
                                </td>
                                <td>{{ formatAmountCurrency(item.unit_price) }}</td>
                                <td style="text-align: right">
                                    {{ formatAmountCurrency(item.subtotal) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice ? 4 : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.order_tax") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.tax_amount) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice ? 4 : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.discount") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.discount) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice ? 4 : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.shipping") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.shipping) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tax-invoice-totals">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 30%">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.items") }}: {{ order.total_items }}
                                </h3>
                            </td>
                            <td style="width: 30%">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.qty") }}: {{ order.total_quantity }}
                                </h3>
                            </td>
                            <td style="width: 40%; text-align: center">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.total") }}:
                                    {{ formatAmountCurrency(order.total) }}
                                </h3>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="paid-amount-deatils">
                    <table style="width: 100%">
                        <thead style="background: #eee">
                            <td style="width: 50%">{{ $t("payments.paid_amount") }}</td>
                            <td style="width: 50%">{{ $t("payments.due_amount") }}</td>
                        </thead>
                        <tbody>
                            <tr class="paid-amount-row">
                                <td>{{ formatAmountCurrency(order.paid_amount) }}</td>
                                <td>{{ formatAmountCurrency(order.due_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <table style="width: 100%">
                        <tr style="text-align: center">
                            <td style="width: 100%">
                                <h4
                                    style="margin-bottom: 0px"
                                    v-if="order.order_payments"
                                >
                                    {{ $t("invoice.payment_mode") }}:
                                    <span
                                        v-for="currentOrderPayments in order.order_payments"
                                        :key="currentOrderPayments.xid"
                                        style="margin-right: 5px"
                                    >
                                        {{
                                            formatAmountCurrency(
                                                currentOrderPayments.amount
                                            )
                                        }}
                                        (<span
                                            v-if="
                                                currentOrderPayments.payment &&
                                                currentOrderPayments.payment
                                                    .payment_mode &&
                                                currentOrderPayments.payment.payment_mode
                                                    .name
                                            "
                                        >
                                            {{
                                                currentOrderPayments.payment.payment_mode
                                                    .name
                                            }}
                                        </span>
                                        )
                                    </span>
                                </h4>
                                <h3 style="margin-bottom: 0px" v-else>
                                    {{ $t("invoice.payment_mode") }}: -
                                </h3>
                            </td>
                        </tr>
                    </table>
                </div>
                <div
                    v-if="selectedWarehouse.show_discount_tax_on_invoice"
                    class="discount-details"
                >
                    <p>
                        {{ $t("invoice.total_discount_on_mrp") }} :
                        {{ formatAmountCurrency(order.saving_on_mrp) }}
                    </p>
                    <p>
                        {{ $t("invoice.total_discount") }} :
                        {{ order.saving_percentage }}%
                    </p>
                    <p>
                        {{ $t("invoice.total_tax") }} :
                        {{ formatAmountCurrency(order.total_tax_on_items) }}
                    </p>
                </div>
                <div class="barcode-details">
                    <BarcodeGenerator
                        :value="order.invoice_number + ''"
                        :height="25"
                        :width="1"
                        :fontSize="15"
                        :elementTag="'svg'"
                    />
                </div>
                <div class="thanks-details">
                    <h3>{{ $t("invoice.thanks_message") }}</h3>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="footer-button">
                <a-button type="primary" @click="printInvoice">
                    <template #icon>
                        <PrinterOutlined />
                    </template>
                    {{ $t("common.print_invoice") }}
                </a-button>
            </div>
        </template>
    </a-modal>
</template>

<script>
import { ref, defineComponent } from "vue";
import { PrinterOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import BarcodeGenerator from "../../../../common/components/barcode/BarcodeGenerator.vue";
const posInvoiceCssUrl = window.config.pos_invoice_css;

export default defineComponent({
    props: ["visible", "order"],
    emits: ["closed", "success"],
    components: {
        PrinterOutlined,
        BarcodeGenerator,
    },
    setup(props, { emit }) {
        const { formatAmountCurrency, formatDate, selectedWarehouse } = common();

        const onClose = () => {
            emit("closed");
        };

        const printInvoice = () => {
            var invoiceContent = document.getElementById("pos-invoice").innerHTML;
            var newWindow = window.open("", "", "height=500, width=500");
            newWindow.document.write(
                `<link rel="stylesheet" href="${posInvoiceCssUrl}"><html><body>`
            );
            newWindow.document.write(invoiceContent);
            newWindow.document.write("</body></html>");
            newWindow.document.close();
            newWindow.print();
        };

        return {
            onClose,
            formatDate,
            selectedWarehouse,
            formatAmountCurrency,
            printInvoice,
        };
    },
});
</script>

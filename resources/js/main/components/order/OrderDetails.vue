<template>
    <a-drawer
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <template #title>
            <a-page-header style="padding: 0px">
                <template #title>
                    #{{ order.invoice_number }}
                    <PaymentStatus :paymentStatus="order.payment_status" />
                </template>
            </a-page-header>
            <a-breadcrumb>
                <div style="width: 50%">
                    <a-progress
                        :percent="
                            parseFloat(
                                parseFloat(
                                    (parseFloat(order.paid_amount) /
                                        parseFloat(order.total)) *
                                        100
                                ).toFixed(2)
                            )
                        "
                    />
                </div>
            </a-breadcrumb>
        </template>
        <template #extra>
            <a-space>
                <a-button
                    v-if="
                        (permsArray.includes('order_payments_create') ||
                            permsArray.includes('admin')) &&
                        order.payment_status != 'paid' &&
                        order.order_type != 'stock-transfers' &&
                        detailsRef
                    "
                    type="primary"
                    @click="detailsRef.addItem"
                >
                    <PlusOutlined />
                    {{ $t("payments.add") }}
                </a-button>
                <a-button
                    v-else-if="
                        (permsArray.includes('order_payments_create') ||
                            permsArray.includes('admin')) &&
                        order.payment_status != 'paid' &&
                        order.order_type == 'stock-transfers' &&
                        order.from_warehouse &&
                        order.from_warehouse.xid &&
                        order.from_warehouse.xid == selectedWarehouse.xid &&
                        detailsRef
                    "
                    type="primary"
                    @click="detailsRef.addItem"
                >
                    <PlusOutlined />
                    {{ $t("payments.add") }}
                </a-button>
                <a-typography-link
                    :href="`${invoiceBaseUrl}/${order.unique_id}`"
                    target="_blank"
                >
                    <a-button type="primary">
                        <DownloadOutlined />
                        {{ $t("sales.invoice") }}
                    </a-button>
                </a-typography-link>
            </a-space>
        </template>
        <div v-if="order && order.xid">
            <Details
                ref="detailsRef"
                :selectedItem="order"
                @goBack="restSelectedItem"
                @reloadOrder="paymentSuccess"
            />
        </div>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
import { PlusOutlined, DownloadOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import PaymentStatus from "../../../common/components/order/PaymentStatus.vue";
import Details from "../../views/stock-management/purchases/Details.vue";

export default defineComponent({
    props: ["visible", "order"],
    emits: ["close", "restSelectedItem", "reloadOrder"],
    components: {
        PaymentStatus,
        Details,
        PlusOutlined,
        DownloadOutlined,
    },
    setup(props, { emit }) {
        const {
            formatAmountCurrency,
            permsArray,
            invoiceBaseUrl,
            selectedWarehouse,
        } = common();
        const { t } = useI18n();
        const detailsRef = ref(null);

        const onClose = () => {
            emit("close");
        };

        const restSelectedItem = () => {
            emit("goBack");
        };

        const paymentSuccess = () => {
            emit("reloadOrder");
        };

        const downloadInvoice = () => {};

        return {
            detailsRef,
            formatAmountCurrency,
            invoiceBaseUrl,
            permsArray,
            selectedWarehouse,
            onClose,
            restSelectedItem,
            paymentSuccess,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",

            downloadInvoice,
        };
    },
});
</script>

<style lang="less">
.product-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>

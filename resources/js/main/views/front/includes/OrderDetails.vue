<template>
    <div v-if="order.cancelled">
        <a-alert
            :message="$t('online_orders.order_cancelled')"
            :description="$t('online_orders.order_cancelled_message')"
            type="error"
            show-icon
        >
            <template #icon><stop-outlined /></template>
        </a-alert>
    </div>
    <div v-else>
        <order-status :orderStatus="order.order_status" />
    </div>

    <div class="item-desc mt-40">
        <span>{{ $t("online_orders.order_summary") }}</span>
    </div>
    <div class="mt-20 pl-15">
        <a-descriptions :title="null" :column="2" :labelStyle="{ fontWeight: 'bold' }">
            <a-descriptions-item :label="$t('stock.order_id')">
                {{ order.invoice_number }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('common.total')">
                {{ formatAmountCurrency(order.total) }}
            </a-descriptions-item>

            <a-descriptions-item :label="$t('user.name')">
                {{ order.shipping_address.name }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('user.email')">
                {{ order.shipping_address.email }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('user.phone')">
                {{ order.shipping_address.phone }}
            </a-descriptions-item>

            <a-descriptions-item :label="$t('payments.payment_status')">
                <PaymentStatus :paymentStatus="order.payment_status" />
            </a-descriptions-item>
            <a-descriptions-item :label="$t('stock.status')">
                <OrderStatusTag :data="order" />
            </a-descriptions-item>

            <a-descriptions-item :label="$t('stock.shipping_address')">
                {{ order.shipping_address.shipping_address }}
            </a-descriptions-item>
            <a-descriptions-item :label="$t('stock.billing_address')">
                {{ order.shipping_address.address }}
            </a-descriptions-item>
        </a-descriptions>
    </div>

    <a-row class="mt-20">
        <a-col :span="24">
            <a-table
                :columns="columns"
                :row-key="(record) => record.id"
                :data-source="order.items"
                :pagination="false"
            >
                <template #bodyCell="{ index, column, record }">
                    <template v-if="column.dataIndex === 'id'">
                        {{ index + 1 }}
                    </template>
                    <template v-if="column.dataIndex === 'product_id'">
                        <a-list-item>
                            <a-list-item-meta>
                                <template #avatar>
                                    <a-avatar
                                        :src="record.product.image_url"
                                        size="large"
                                        shape="square"
                                    />
                                </template>
                                <template #title>
                                    {{ record.product.name }}
                                </template>
                            </a-list-item-meta>
                        </a-list-item>
                    </template>
                    <template v-if="column.dataIndex === 'unit_price'">
                        {{ formatAmountCurrency(record.unit_price) }}
                    </template>
                    <template v-if="column.dataIndex === 'subtotal'">
                        {{ formatAmountCurrency(record.subtotal) }}
                    </template>
                </template>
            </a-table>
        </a-col>
    </a-row>

    <a-row class="mt-30">
        <a-col :span="18"></a-col>
        <a-col :span="6">
            <div class="pd-10">
                <a-row>
                    <a-col :span="12">{{ $t("product.subtotal") }}</a-col>
                    <a-col :span="12" class="text-right">
                        {{ formatAmountCurrency(order.subtotal) }}
                    </a-col>
                </a-row>
                <a-row class="mt-10">
                    <a-col :span="12">{{ $t("product.discount") }}</a-col>
                    <a-col :span="12" class="text-right">
                        {{ formatAmountCurrency(order.discount) }}
                    </a-col>
                </a-row>
                <a-row class="mt-10">
                    <a-col :span="12">{{ $t("stock.order_tax") }}</a-col>
                    <a-col :span="12" class="text-right">
                        {{ formatAmountCurrency(order.tax_amount) }}
                    </a-col>
                </a-row>
                <a-row class="mt-10">
                    <a-col :span="12">{{ $t("stock.shipping") }}</a-col>
                    <a-col :span="12" class="text-right">
                        {{ formatAmountCurrency(order.shipping) }}
                    </a-col>
                </a-row>
            </div>
            <div class="item-total pd-10">
                <a-row class="mt-10">
                    <a-col :span="12">{{ $t("stock.grand_total") }}</a-col>
                    <a-col :span="12" class="text-right">
                        {{ formatAmountCurrency(order.total) }}
                    </a-col>
                </a-row>
            </div>
        </a-col>
    </a-row>
</template>
<script>
import { defineComponent, ref, computed, onMounted } from "vue";
import { StopOutlined } from "@ant-design/icons-vue";
import cart from "../../../../common/composable/cart";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";
import OrderStatus from "./OrderStatus.vue";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import { buildAddress } from "../../../../common/scripts/functions";
import OrderStatusTag from "../../../../common/components/order/OrderStatus.vue";

export default defineComponent({
    props: {
        order: {
            default: {},
        },
        detailView: {
            default: "front",
        },
    },
    components: {
        OrderStatus,
        PaymentStatus,
        StopOutlined,
        OrderStatusTag,
    },
    setup(props, { emit }) {
        const cartObj = cart();
        const commonObj = common();
        const store = useStore();
        const warehouseCurrency = computed(() => store.state.front.warehouseCurrency);
        const columns = [
            {
                title: "#",
                dataIndex: "id",
            },
            {
                title: "Product",
                dataIndex: "product_id",
            },
            {
                title: "Quantity",
                dataIndex: "quantity",
            },
            {
                title: "Price",
                dataIndex: "unit_price",
            },
            {
                title: "Total",
                dataIndex: "subtotal",
            },
        ];

        const formatAmountCurrency = (amount) => {
            if (props.detailView == "admin") {
                return commonObj.formatAmountCurrency(amount);
            } else {
                return cartObj.formatAmountCurrency(amount);
            }
        };

        return {
            columns,
            formatAmountCurrency,
            buildAddress,
            warehouseCurrency,
        };
    },
});
</script>

<style lang="less">
.item-desc {
    background-color: #f5f5f5;
    padding: 15px;

    span {
        font-weight: 600;
        font-size: 18px;
    }
}

.item-total {
    background-color: #f5f5f5;
    font-weight: bold;
}
</style>

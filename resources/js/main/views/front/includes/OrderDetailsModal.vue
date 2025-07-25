<template>
    <a-menu-item @click="showModal">
        <eye-outlined />
        {{ $t("stock.view_order") }}
    </a-menu-item>
    <a-modal v-model:open="visible" width="70%" :footer="false">
        <template #title>
            {{ order.invoice_number }}
        </template>

        <OrderDetails :order="order" />
    </a-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import { EyeOutlined, StopOutlined } from "@ant-design/icons-vue";
import cart from "../../../../common/composable/cart";
import OrderStatus from "./OrderStatus.vue";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import { buildAddress } from "../../../../common/scripts/functions";
import OrderDetails from "./OrderDetails.vue";

export default defineComponent({
    props: ["order"],
    components: {
        EyeOutlined,
        StopOutlined,
        OrderStatus,
        PaymentStatus,
        OrderDetails,
    },
    setup() {
        const { formatAmountCurrency } = cart();
        const visible = ref(false);
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

        const showModal = () => {
            visible.value = true;
        };

        return {
            visible,
            showModal,

            columns,
            formatAmountCurrency,
            buildAddress,
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

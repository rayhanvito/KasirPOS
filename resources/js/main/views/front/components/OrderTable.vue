<template>
    <a-table
        :columns="columns"
        :row-key="(record) => record.id"
        :data-source="data"
        :pagination="false"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'order_date'">
                {{ formatDateTime(record.order_date) }}
            </template>
            <template v-if="column.dataIndex === 'total'">
                {{ formatAmountCurrency(record.total) }}
            </template>
            <template v-if="column.dataIndex === 'payment_status'">
                <PaymentStatus :paymentStatus="record.payment_status" />
            </template>
            <template v-if="column.dataIndex === 'order_status'">
                <OrderStatus :data="record" />
            </template>
            <template v-if="column.dataIndex === 'action'">
                <a-dropdown placement="bottomRight">
                    <template #overlay>
                        <a-menu>
                            <OrderDetailsModal :order="record" />

                            <a-menu-item
                                @click="cancelOrder(record.unique_id)"
                                v-if="
                                    record.order_status == 'ordered' && !record.cancelled
                                "
                            >
                                <stop-outlined />
                                Cancel Order
                            </a-menu-item>

                            <a-menu-item @click="downloadInvoice(record.unique_id)">
                                <download-outlined />
                                Download Invoice
                            </a-menu-item>
                        </a-menu>
                    </template>
                    <more-outlined
                        :style="{
                            fontSize: '16px',
                        }"
                    />
                </a-dropdown>
            </template>
        </template>
    </a-table>
</template>

<script>
import { defineComponent, ref, createVNode } from "vue";
import {
    EyeOutlined,
    DeleteOutlined,
    MoreOutlined,
    StopOutlined,
    DownloadOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import OrderStatus from "../../../../common/components/order/OrderStatus.vue";
import OrderDetailsModal from "../includes/OrderDetailsModal.vue";
import common from "../../../../common/composable/common";
import cart from "../../../../common/composable/cart";

export default defineComponent({
    props: ["data"],
    emits: ["reloadOrders"],
    components: {
        EyeOutlined,
        DeleteOutlined,
        MoreOutlined,
        StopOutlined,
        DownloadOutlined,
        PaymentStatus,
        OrderStatus,
        OrderDetailsModal,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const { formatDateTime } = common();
        const { formatAmountCurrency } = cart();
        const columns = [
            {
                title: "Date",
                dataIndex: "order_date",
            },
            {
                title: "Total",
                dataIndex: "total",
            },
            {
                title: "Order Status",
                dataIndex: "order_status",
            },
            {
                title: "Payment Status",
                dataIndex: "payment_status",
            },
            {
                title: "Action",
                dataIndex: "action",
            },
        ];

        const cancelOrder = (uniqueId) => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("stock.order_cancel_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosFront
                        .post(`front/self/cancel-order/${uniqueId}`)
                        .then((response) => {
                            emit("reloadOrders");

                            notification.success({
                                message: t("common.success"),
                                description: t("stock.order_canceled"),
                                placement: "bottomRight",
                            });
                        });
                },
                onCancel() {},
            });
        };

        const downloadInvoice = (invoiceUniqueId) => {
            window.location.href = window.config.path + "/api/v1/pdf/" + invoiceUniqueId;
        };

        return {
            columns,
            cancelOrder,
            downloadInvoice,
            formatAmountCurrency,
            formatDateTime,
        };
    },
});
</script>

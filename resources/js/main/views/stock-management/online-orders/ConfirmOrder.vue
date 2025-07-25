<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="$t('online_orders.update_orders_status')"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-table
                        :columns="confirmOrderTableColumns"
                        :data-source="data.items"
                        :row-key="(record) => record.id"
                        :pagination="false"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'product_id'">
                                {{ record.product.name }}
                            </template>
                            <template v-if="column.dataIndex === 'quantity'">
                                {{
                                    `${record.quantity} ${record.product.unit.short_name}`
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'current_stock'">
                                <a-typography-text
                                    v-if="
                                        record.product.details.current_stock <
                                        record.quantity
                                    "
                                    type="danger"
                                    strong
                                >
                                    {{
                                        `${record.product.details.current_stock} ${record.product.unit.short_name}`
                                    }}
                                </a-typography-text>
                                <a-typography-text
                                    v-else-if="
                                        record.product.details.current_stock >=
                                        record.quantity
                                    "
                                    type="success"
                                    strong
                                >
                                    {{
                                        `${record.product.details.current_stock} ${record.product.unit.short_name}`
                                    }}
                                </a-typography-text>
                            </template>
                        </template>
                    </a-table>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <CheckOutlined />
                </template>
                {{ $t("common.confirm") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import { LoadingOutlined, CheckOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible"],
    emits: ["confirmSuccess", "closed"],
    components: {
        LoadingOutlined,
        CheckOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const confirmOrderTableColumns = ref([
            {
                title: t("product.product"),
                dataIndex: "product_id",
            },
            {
                title: t("product.quantity"),
                dataIndex: "quantity",
            },
            {
                title: t("stock_adjustment.current_stock"),
                dataIndex: "current_stock",
            },
        ]);

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: `online-orders/confirm/${props.data.unique_id}`,
                data: {},
                successMessage: t("online_orders.order_confirmed"),
                success: (res) => {
                    emit("closed");
                    emit("confirmSuccess");
                },
            });
        };

        return {
            rules,
            loading,
            onClose,
            onSubmit,
            confirmOrderTableColumns,
        };
    },
});
</script>

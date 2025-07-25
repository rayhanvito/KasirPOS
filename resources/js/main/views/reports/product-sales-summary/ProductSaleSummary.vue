<template>
    <a-row>
        <a-col :span="24">
            <div v-if="!table.loading" class="table-responsive">
                <a-table
                    :columns="columns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    id="product-sales-summary-reports-table"
                    bordered
                    size="middle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'name'">
                            <a-badge>
                                <a-avatar
                                    shape="square"
                                    :src="record.product.image_url"
                                />
                                {{ record.product.name }}
                            </a-badge>
                        </template>
                        <template v-if="column.dataIndex === 'unit_sold'">
                            {{ `${record.unit_sold} ${record.product.unit.short_name}` }}
                        </template>
                        <template v-if="column.dataIndex === 'total_purchase_price'">
                            {{
                                formatAmountCurrency(
                                    record.unit_sold *
                                        record.product.details.purchase_price
                                )
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'total_sales_price'">
                            {{ formatAmountCurrency(record.total_sales_price) }}
                        </template>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell :col-span="2"> </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ $t("common.total") }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.totalPurchasePrice) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                            <a-table-summary-cell :col-span="1">
                                <a-typography-text strong>
                                    {{ formatAmountCurrency(totals.totalSalesPrice) }}
                                </a-typography-text>
                            </a-table-summary-cell>
                        </a-table-summary-row>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";

export default defineComponent({
    props: {
        product_id: null,
        category_id: null,
        dates: {
            default: [],
            type: null,
        },
    },
    components: {
        UserInfo,
        PaymentStatus,
    },
    setup(props) {
        const { columns, hashableColumns } = fields();
        const { formatDateTime, formatAmountCurrency, selectedWarehouse } = common();
        const datatableVariables = datatable();

        onMounted(() => {
            datatableVariables.table.pagination = {
                pageSize: 10000,
                showSizeChanger: false,
            };

            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const filters = {};

            if (propsData.product_id && propsData.product_id != undefined) {
                filters.product_id = propsData.product_id;
            }

            datatableVariables.tableUrl.value = {
                url: "order-items?fields=id",
                filters,
                extraFilters: {
                    product_sales_summary: true,
                    dates: propsData.dates,
                    category_id: propsData.category_id,
                },
            };
            datatableVariables.hashable.value = [...hashableColumns];
            datatableVariables.table.sorter = { field: "products.name", order: "asc" };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "product_sales_summary_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        const totals = computed(() => {
            let totalPurchasePrice = 0;
            let totalSalesPrice = 0;
            datatableVariables.table.data.forEach((tableRowData) => {
                totalPurchasePrice +=
                    tableRowData.product.details.purchase_price * tableRowData.unit_sold;
                totalSalesPrice += tableRowData.total_sales_price;
            });
            return {
                totalPurchasePrice,
                totalSalesPrice,
            };
        });

        watch(props, (newVal, oldVal) => {
            getData(newVal);
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getData(props);
        });

        return {
            columns,
            ...datatableVariables,

            formatDateTime,
            formatAmountCurrency,
            totals,
        };
    },
});
</script>

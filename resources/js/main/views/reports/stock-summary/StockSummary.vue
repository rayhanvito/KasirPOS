<template>
    <a-row>
        <a-col :span="24">
            <div v-if="!table.loading" class="table-responsive">
                <a-table
                    :columns="summaryColumns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    id="stock-summary-reports-table"
                    bordered
                    size="middle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'name'">
                            <a-badge>
                                <a-avatar shape="square" :src="record.image_url" />
                                {{ record.name }}
                            </a-badge>
                        </template>
                        <template v-if="column.dataIndex === 'category_id'">
                            {{ record.category.name }}
                        </template>
                        <template v-if="column.dataIndex === 'brand_id'">
                            {{
                                record.brand && record.brand.xid ? record.brand.name : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'sales_price'">
                            {{ formatAmountCurrency(record.details.sales_price) }}
                        </template>
                        <template v-if="column.dataIndex === 'purchase_price'">
                            {{ formatAmountCurrency(record.details.purchase_price) }}
                        </template>
                        <template v-if="column.dataIndex === 'current_stock'">
                            {{
                                `${record.details.current_stock} ${record.unit.short_name}`
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'stock_value'">
                            {{ $t("product.by_purchase") }} :
                            <a-typography-text strong>{{
                                formatAmountCurrency(
                                    record.details.current_stock *
                                        record.details.purchase_price
                                )
                            }}</a-typography-text>
                            <br />
                            {{ $t("product.by_sales") }} :
                            <a-typography-text strong>{{
                                formatAmountCurrency(
                                    record.details.current_stock *
                                        record.details.sales_price
                                )
                            }}</a-typography-text>
                        </template>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";

export default defineComponent({
    props: {
        category_id: null,
        brand_id: null,
    },
    components: {
        UserInfo,
        PaymentStatus,
    },
    setup(props) {
        const { summaryColumns, hashableColumns } = fields();
        const { formatDateTime, formatAmountCurrency, selectedWarehouse } = common();
        const datatableVariables = datatable();

        onMounted(() => {
            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const filters = {};

            if (propsData.category_id && propsData.category_id != undefined) {
                filters.category_id = propsData.category_id;
            }

            if (propsData.brand_id && propsData.brand_id != undefined) {
                filters.brand_id = propsData.brand_id;
            }

            datatableVariables.tableUrl.value = {
                url:
                    "products?fields=id,xid,name,,item_code,image,image_url,category_id,x_category_id,category{id,xid,name},brand_id,x_brand_id,brand{id,xid,name},unit_id,x_unit_id,unit{id,xid,name,short_name},details{stock_quantitiy_alert,opening_stock,opening_stock_date,wholesale_price,wholesale_quantity,mrp,purchase_price,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type,current_stock,warehouse_id,x_warehouse_id,status},details:tax{id,xid,name,rate}",
                filters,
            };
            datatableVariables.hashable.value = [...hashableColumns];
            datatableVariables.table.sorter = { field: "name", order: "asc" };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "stock_summary_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        watch(props, (newVal, oldVal) => {
            getData(newVal);
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getData(props);
        });

        return {
            summaryColumns,
            ...datatableVariables,

            formatDateTime,
            formatAmountCurrency,
        };
    },
});
</script>

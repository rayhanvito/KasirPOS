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
                    id="rate-list-reports-table"
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
                            {{
                                record.category && record.category.name
                                    ? record.category.name
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'brand_id'">
                            {{
                                record.brand && record.brand.name
                                    ? record.brand.name
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'mrp'">
                            {{
                                record.details.mrp
                                    ? formatAmountCurrency(record.details.mrp)
                                    : "-"
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'sales_price'">
                            {{ formatAmountCurrency(record.details.sales_price) }}
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
        product_id: null,
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
            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const extraFilters = {};

            if (propsData.product_id && propsData.product_id != undefined) {
                extraFilters.x_id = propsData.product_id;
            }

            datatableVariables.tableUrl.value = {
                url:
                    "products?fields=id,xid,name,,item_code,image,image_url,category_id,x_category_id,category{id,xid,name},brand_id,x_brand_id,brand{id,xid,name},unit_id,x_unit_id,unit{id,xid,name,short_name},details{mrp,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type},details:tax{id,xid,name,rate}",
                extraFilters,
            };
            datatableVariables.hashable.value = [...hashableColumns];
            datatableVariables.table.sorter = { field: "name", order: "asc" };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "rate_list_reports",
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
            columns,
            ...datatableVariables,

            formatDateTime,
            formatAmountCurrency,
        };
    },
});
</script>

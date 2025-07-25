<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.stock_alert`)" class="p-0">
                <template #extra>
                    <ExprotTable
                        exportType="stock_alert_reports"
                        tableName="stock-alert-reports-table"
                        :title="`${$t('menu.stock_alert')} ${$t('menu.reports')}`"
                    />
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.reports`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.stock_alert`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="6">
                        <ProductSearchInput
                            @valueChanged="
                                (productId) => {
                                    searchProductId = productId;
                                    getTableData();
                                }
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :columns="stockAlertColumns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        id="stock-alert-reports-table"
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
                            <template v-if="column.dataIndex === 'current_stock'">
                                {{
                                    `${record.details.current_stock} ${record.unit.short_name}`
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'stock_quantitiy_alert'">
                                {{
                                    `${record.details.stock_quantitiy_alert} ${record.unit.short_name}`
                                }}
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, onBeforeMount, watch } from "vue";
import { useRouter } from "vue-router";
import ProductSearchInput from "../../../../common/components/product/ProductSearchInput.vue";
import common from "../../../../common/composable/common";
import datatable from "../../../../common/composable/datatable";
import fields from "./fields";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ExprotTable from "../../../components/report-exports/ExportTable.vue";

export default {
    components: {
        ProductSearchInput,
        AdminPageHeader,
        ExprotTable,
    },
    setup() {
        const { permsArray, selectedWarehouse, willSubscriptionModuleVisible } = common();
        const { url, stockAlertColumns, stockAlertHashableColumns } = fields();
        const searchProductId = ref(undefined);
        const router = useRouter();
        const datatableVariables = datatable();

        onBeforeMount(() => {
            if (
                !(
                    permsArray.value.includes("products_view") ||
                    permsArray.value.includes("admin")
                ) ||
                !willSubscriptionModuleVisible("reports")
            ) {
                router.push("admin.dashboard.index");
            }
        });

        onMounted(() => {
            getTableData();
        });

        const getTableData = () => {
            datatableVariables.tableUrl.value = {
                url,
                filters: {
                    "products.id": searchProductId.value,
                },
                extraFilters: {
                    fetch_stock_alert: true,
                },
            };
            datatableVariables.hashable.value = [...stockAlertHashableColumns];
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "stock_alert_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        watch(selectedWarehouse, (newVal, oldVal) => {
            getTableData();
        });

        return {
            stockAlertColumns,
            ...datatableVariables,

            searchProductId,
            getTableData,
            permsArray,
        };
    },
};
</script>

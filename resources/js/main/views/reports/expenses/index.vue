<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.expense_reports`)" class="p-0">
                <template #extra>
                    <ExprotTable
                        exportType="expense_reports"
                        tableName="expense-reports-table"
                        :title="`${$t('menu.expenses')} ${$t('menu.reports')}`"
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
                    {{ $t(`menu.expense_reports`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.expense_category_id"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('expense.expense_category'),
                                ])
                            "
                            @change="getTableData"
                            :allowClear="true"
                            optionFilterProp="label"
                        >
                            <a-select-option
                                v-for="expenseCategory in expenseCategories"
                                :key="expenseCategory.xid"
                                :value="expenseCategory.xid"
                                :label="expenseCategory.name"
                            >
                                {{ expenseCategory.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.user_id"
                            show-search
                            style="width: 100%"
                            :placeholder="
                                $t('common.select_default_text', [$t('expense.user')])
                            "
                            @change="getTableData"
                            :allowClear="true"
                            optionFilterProp="label"
                        >
                            <a-select-option
                                v-for="staffMember in staffMembers"
                                :key="staffMember.xid"
                                :value="staffMember.xid"
                                :label="staffMember.name"
                            >
                                {{ staffMember.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                        <DateRangePicker
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    extraFilters.dates = changedDateTime;
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
                        :columns="expenseColumns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        id="expense-reports-table"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'expense_category_id'">
                                {{ record.expense_category.name }}
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                {{ formatAmountCurrency(record.amount) }}
                            </template>
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                {{ record.user?.name }}
                            </template>
                        </template>
                        <template #summary>
                            <a-table-summary-row>
                                <a-table-summary-cell :col-span="2">
                                </a-table-summary-cell>
                                <a-table-summary-cell :col-span="1">
                                    <a-typography-text strong>
                                        {{ $t("common.total") }}
                                    </a-typography-text>
                                </a-table-summary-cell>
                                <a-table-summary-cell :col-span="1">
                                    <a-typography-text strong>
                                        {{ formatAmountCurrency(totals.totalAmount) }}
                                    </a-typography-text>
                                </a-table-summary-cell>
                            </a-table-summary-row>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, onBeforeMount, watch, computed } from "vue";
import { useRouter } from "vue-router";
import ProductSearchInput from "../../../../common/components/product/ProductSearchInput.vue";
import common from "../../../../common/composable/common";
import datatable from "../../../../common/composable/datatable";
import fields from "./fields";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ExprotTable from "../../../components/report-exports/ExportTable.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";

export default {
    components: {
        ProductSearchInput,
        AdminPageHeader,
        ExprotTable,
        DateRangePicker,
    },
    setup() {
        const {
            permsArray,
            selectedWarehouse,
            formatAmountCurrency,
            formatDate,
            willSubscriptionModuleVisible,
        } = common();
        const { url, expenseColumns, expenseHashableColumns } = fields();
        const searchProductId = ref(undefined);
        const router = useRouter();
        const datatableVariables = datatable();
        const filters = ref({
            expense_category_id: undefined,
            user_id: undefined,
        });
        const expenseCategories = ref([]);
        const staffMembers = ref([]);
        const extraFilters = ref({
            dates: [],
        });

        onBeforeMount(() => {
            if (
                !(
                    permsArray.value.includes("expenses_view") ||
                    permsArray.value.includes("admin")
                ) ||
                !willSubscriptionModuleVisible("reports")
            ) {
                router.push("admin.dashboard.index");
            }
        });

        onMounted(() => {
            const expenseCategoriesPromise = axiosAdmin.get(
                "expense-categories?limit=10000"
            );
            const staffMembersPromise = axiosAdmin.get("users?limit=10000");

            Promise.all([expenseCategoriesPromise, staffMembersPromise]).then(
                ([expenseCategoriesResponse, staffMembersResponse]) => {
                    expenseCategories.value = expenseCategoriesResponse.data;
                    staffMembers.value = staffMembersResponse.data;
                }
            );

            getTableData();
        });

        const getTableData = () => {
            datatableVariables.tableUrl.value = {
                url,
                filters,
                extraFilters,
            };
            datatableVariables.hashable.value = [...expenseHashableColumns];
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "expense_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        const totals = computed(() => {
            let totalAmount = 0;
            datatableVariables.table.data.forEach((tableRowData) => {
                totalAmount += tableRowData.amount;
            });
            return {
                totalAmount,
            };
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getTableData();
        });

        return {
            expenseColumns,
            ...datatableVariables,

            searchProductId,
            getTableData,
            permsArray,
            filters,
            extraFilters,

            expenseCategories,
            staffMembers,
            formatAmountCurrency,
            formatDate,

            totals,
        };
    },
};
</script>

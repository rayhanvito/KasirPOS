<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.profit_loss`)" class="p-0">
                <template #extra>
                    <ExprotTable
                        v-if="selectedTab == 'by_order'"
                        exportType="profit_loss_reports"
                        tableName="profit-loss-reports-table"
                        :title="`${$t('menu.profit_loss')} ${$t('menu.reports')}`"
                    />
                    <ExprotTable
                        v-if="
                            selectedTab == 'by_dates' && dateWiseReportResults.length > 0
                        "
                        exportType="profit_loss_reports_by_dates"
                        tableName="profit-loss-reports-by-dates-table"
                        :title="`${$t('menu.profit_loss')} ${$t('menu.reports')}`"
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
                    {{ $t(`menu.profit_loss`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="8">
                        <DateRangePicker
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    filters.dates = changedDateTime;
                                }
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-tabs v-model:activeKey="selectedTab">
            <a-tab-pane key="by_order">
                <template #tab>
                    <ShoppingCartOutlined />
                    {{ $t("report.by_order") }}
                </template>
                <a-row id="profit-loss-reports-table">
                    <a-col :span="24" class="mt-10">
                        <a-descriptions
                            :title="$t('common.profit_reports_by_orders')"
                            :column="1"
                            bordered
                            size="middle"
                        >
                            <a-descriptions-item>
                                <template #label>
                                    <strong>{{ $t("common.particulars") }}</strong>
                                </template>
                                <strong>{{ $t("common.amount") }}</strong>
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label> {{ $t("menu.sales") }} (+) </template>
                                {{ formatAmountCurrency(reportData.sales) }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("menu.purchases") }} (-)
                                </template>
                                {{ formatAmountCurrency(reportData.purchases) }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("menu.purchase_returns") }} (+)
                                </template>
                                {{ formatAmountCurrency(reportData.purchase_returns) }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("menu.sales_returns") }} (-)
                                </template>
                                {{ formatAmountCurrency(reportData.sales_returns) }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("stock_transfer.stock_transfer_transfered") }}
                                    (+)
                                </template>
                                {{
                                    formatAmountCurrency(
                                        reportData.stock_transfer_transfered
                                    )
                                }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("stock_transfer.stock_transfer_received") }} (-)
                                </template>
                                {{
                                    formatAmountCurrency(
                                        reportData.stock_transfer_received
                                    )
                                }}
                            </a-descriptions-item>
                            <a-descriptions-item>
                                <template #label>
                                    {{ $t("menu.expenses") }} (-)
                                </template>
                                {{ formatAmountCurrency(reportData.expenses) }}
                            </a-descriptions-item>
                            <a-descriptions-item :label="$t('common.profit')">
                                <a-typography-text
                                    :type="reportData.profit > 0 ? 'success' : 'danger'"
                                    strong
                                >
                                    {{ formatAmountCurrency(reportData.profit) }}
                                </a-typography-text>
                            </a-descriptions-item>
                        </a-descriptions>
                    </a-col>
                </a-row>
            </a-tab-pane>
            <a-tab-pane key="by_dates">
                <template #tab>
                    <ClockCircleOutlined />
                    {{ $t("report.by_dates") }}
                </template>
                <div
                    id="profit-loss-reports-by-dates-table"
                    v-if="dateWiseReportResults.length > 0"
                >
                    <a-row
                        v-for="(
                            dateWiseReportResult, resultIndex, resultKey
                        ) in dateWiseReportResults"
                        :key="dateWiseReportResult.date"
                    >
                        <a-col :span="24" :class="['mt-10', `tbl_result_${resultIndex}`]">
                            <a-descriptions
                                style="margin-top: 10px"
                                :column="1"
                                bordered
                                size="middle"
                            >
                                <a-descriptions-item>
                                    <template #label>
                                        <strong>
                                            {{ formatDate(dateWiseReportResult.date) }}
                                        </strong>
                                    </template>
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        <strong>{{ $t("common.particulars") }}</strong>
                                    </template>
                                    <strong>{{ $t("common.amount") }}</strong>
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("menu.sales") }} (+)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result.sales
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("menu.purchases") }} (-)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result.purchases
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("menu.purchase_returns") }} (+)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result.purchase_returns
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("menu.sales_returns") }} (-)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result.sales_returns
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{
                                            $t("stock_transfer.stock_transfer_transfered")
                                        }}
                                        (+)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result
                                                .stock_transfer_transfered
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("stock_transfer.stock_transfer_received") }}
                                        (-)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result
                                                .stock_transfer_received
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item>
                                    <template #label>
                                        {{ $t("menu.expenses") }} (-)
                                    </template>
                                    {{
                                        formatAmountCurrency(
                                            dateWiseReportResult.result.expenses
                                        )
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('common.profit')">
                                    <a-typography-text
                                        :type="
                                            dateWiseReportResult.result.profit > 0
                                                ? 'success'
                                                : 'danger'
                                        "
                                        strong
                                    >
                                        {{
                                            formatAmountCurrency(
                                                dateWiseReportResult.result.profit
                                            )
                                        }}
                                    </a-typography-text>
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-col>
                    </a-row>
                </div>
                <a-row v-else>
                    <a-col :span="24" class="mt-10">
                        <a-result
                            :title="$t('report.select_date')"
                            :sub-title="$t('report.select_date_message')"
                        />
                    </a-col>
                </a-row>
            </a-tab-pane>
        </a-tabs>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, onBeforeMount, reactive, watch } from "vue";
import {
    BarChartOutlined,
    UnorderedListOutlined,
    ClockCircleOutlined,
    ShoppingCartOutlined,
} from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import { filter } from "lodash-es";
import ProductSearchInput from "../../../../common/components/product/ProductSearchInput.vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ExprotTable from "../../../components/report-exports/ExportTable.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";

export default {
    components: {
        BarChartOutlined,
        UnorderedListOutlined,
        ShoppingCartOutlined,
        ClockCircleOutlined,

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
        const router = useRouter();
        const filters = reactive({
            dates: [],
            active_report_type: "daily_income",
        });
        const reportData = ref([]);
        const store = useStore();
        const selectedTab = ref("by_order");
        const dateWiseReportResults = ref([]);

        onBeforeMount(() => {
            if (
                !(
                    (permsArray.value.includes("products_view") &&
                        permsArray.value.includes("purchases_view") &&
                        permsArray.value.includes("sales_view") &&
                        permsArray.value.includes("purchase_returns_view") &&
                        permsArray.value.includes("sales_returns_view")) ||
                    permsArray.value.includes("admin")
                ) ||
                !willSubscriptionModuleVisible("reports")
            ) {
                router.push("admin.dashboard.index");
            }
        });

        onMounted(() => {
            getData(filters);
        });

        const getData = (filterDate) => {
            axiosAdmin.post("reports/profit-loss", filterDate).then((response) => {
                reportData.value = response.data.results;
                dateWiseReportResults.value = response.data.dates;

                // For Export
                var exportDataType = "profit_loss_reports";
                var allExportDatas = store.state.auth.allExportData;
                var allExportDataExceptType = filter(
                    allExportDatas,
                    (allExportData) => allExportData.export_type != exportDataType
                );

                var newFinalData = filter(
                    allExportDataExceptType,
                    (allExportDataNew) =>
                        allExportDataNew.export_type != "profit_loss_reports_by_dates"
                );

                store.commit("auth/updatAllExportData", [
                    ...newFinalData,
                    {
                        export_type: exportDataType,
                        data: [response.data.results],
                        url: "reports/profit-loss",
                    },
                    {
                        export_type: "profit_loss_reports_by_dates",
                        data: response.data.dates,
                        url: "reports/profit-loss",
                    },
                ]);
            });
        };

        watch(filters, (newVal, oldVal) => {
            getData(newVal);
        });

        return {
            permsArray,
            formatAmountCurrency,
            formatDate,
            filters,
            reportData,
            dateWiseReportResults,

            selectedTab,
        };
    },
};
</script>

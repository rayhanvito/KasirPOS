<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.product_sales_summary`)" class="p-0">
                <template #extra>
                    <ExprotTable
                        exportType="product_sales_summary_reports"
                        tableName="product-sales-summary-reports-table"
                        :title="`${$t('menu.product_sales_summary')} ${$t(
                            'menu.reports'
                        )}`"
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
                    {{ $t(`menu.product_sales_summary`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10"> </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                        <ProductSearchInput
                            @valueChanged="
                                (productId) => {
                                    filters.product_id = productId;
                                }
                            "
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-tree-select
                            v-model:value="filters.category_id"
                            show-search
                            style="width: 100%"
                            :dropdown-style="{ maxHeight: '250px', overflow: 'auto' }"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('category.category'),
                                ])
                            "
                            :tree-data="categories"
                            allow-clear
                            tree-default-expand-all
                            :filterTreeNode="filterTreeNode"
                        />
                    </a-col>

                    <a-col :xs="24" :sm="24" :md="10" :lg="6" :xl="6">
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
        <ProductSaleSummary
            :product_id="filters.product_id"
            :dates="filters.dates"
            :category_id="filters.category_id"
        />
    </admin-page-table-content>
</template>
<script>
import { onMounted, onBeforeMount, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import table from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ProductSaleSummary from "./ProductSaleSummary.vue";
import ProductSearchInput from "../../../../common/components/product/ProductSearchInput.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import ExprotTable from "../../../components/report-exports/ExportTable.vue";

export default {
    components: {
        AdminPageHeader,
        ProductSaleSummary,
        ProductSearchInput,
        DateRangePicker,
        ExprotTable,
    },
    setup() {
        const datatable = table();
        const {
            permsArray,
            getRecursiveCategories,
            filterTreeNode,
            willSubscriptionModuleVisible,
        } = common();
        const router = useRouter();
        const filters = reactive({
            product_id: undefined,
            category_id: undefined,
            dates: [],
        });
        const categories = ref([]);

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
            const categoriesPromise = axiosAdmin.get("categories?limit=10000");

            Promise.all([categoriesPromise]).then(([categoriesResponse]) => {
                categories.value = getRecursiveCategories(categoriesResponse);
            });
        });

        return {
            ...datatable,
            permsArray,
            filters,
            filterTreeNode,
            categories,
        };
    },
};
</script>

<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.stock_summary`)" class="p-0">
                <template #extra>
                    <ExprotTable
                        exportType="stock_summary_reports"
                        tableName="stock-summary-reports-table"
                        :title="`${$t('menu.stock_summary')} ${$t('menu.reports')}`"
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
                    {{ $t(`menu.stock_summary`) }}
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.brand_id"
                            :placeholder="
                                $t('common.select_default_text', [$t('product.brand')])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                        >
                            <a-select-option
                                v-for="brand in brands"
                                :key="brand.xid"
                                :title="brand.name"
                                :value="brand.xid"
                            >
                                {{ brand.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <StockSummary :category_id="filters.category_id" :brand_id="filters.brand_id" />
    </admin-page-table-content>
</template>
<script>
import { onMounted, onBeforeMount, ref, reactive } from "vue";
import { useRouter } from "vue-router";
import table from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import StockSummary from "./StockSummary.vue";
import ExprotTable from "../../../components/report-exports/ExportTable.vue";

export default {
    components: {
        AdminPageHeader,
        StockSummary,
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
        const filters = reactive({
            category_id: undefined,
            brand_id: undefined,
        });
        const categories = ref([]);
        const brands = ref([]);
        const router = useRouter();

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
            getInitialData();
        });

        const getInitialData = () => {
            const categoriesPromise = axiosAdmin.get("categories?limit=10000");
            const brandsPromise = axiosAdmin.get("brands?limit=10000");

            Promise.all([categoriesPromise, brandsPromise]).then(
                ([categoriesResponse, brandsResponse]) => {
                    categories.value = getRecursiveCategories(categoriesResponse);
                    brands.value = brandsResponse.data;
                }
            );
        };

        return {
            ...datatable,
            filters,
            categories,
            brands,
            permsArray,
            filterTreeNode,
        };
    },
};
</script>

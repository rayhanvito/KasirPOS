<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.products`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.product_manager`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.products`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('products_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <SubscriptionModuleVisibility moduleName="product">
                            <a-button
                                type="primary"
                                @click="
                                    () => {
                                        formData = {
                                            ...formData,
                                            product_type: productType,
                                        };
                                        addItem();
                                    }
                                "
                            >
                                <PlusOutlined />
                                {{ $t("product.add") }}
                            </a-button>
                        </SubscriptionModuleVisibility>
                        <ImportProducts
                            :pageTitle="$t('product.import_products')"
                            :sampleFileUrl="sampleFileUrl"
                            importUrl="products/import"
                            @onUploadSuccess="setUrlData"
                        />
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('products_delete') ||
                                permsArray.includes('admin'))
                        "
                        type="primary"
                        @click="showSelectedDeleteConfirm"
                        danger
                    >
                        <template #icon><DeleteOutlined /></template>
                        {{ $t("common.delete") }}
                    </a-button>
                </a-space>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-input-search
                            v-model:value="table.searchString"
                            show-search
                            @change="onTableSearch"
                            @search="onTableSearch"
                            style="width: 100%"
                            :loading="table.filterLoading"
                            :placeholder="$t('common.search')"
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-select
                            v-model:value="filters.brand_id"
                            :placeholder="
                                $t('common.select_default_text', [$t('product.brand')])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
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
                            @change="setUrlData"
                    /></a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <AddEdit
            :addEditType="addEditType"
            :visible="addEditVisible"
            :url="addEditUrl"
            @addEditSuccess="addEditSuccess"
            @addAndNewSuccess="addAndNewSuccess"
            @addAndContinueSuccess="addAndContinueSuccess"
            @closed="onCloseAddEdit"
            :formData="formData"
            :data="viewData"
            :pageTitle="pageTitle"
            :successMessage="successMessage"
        />

        <ProductView
            :product="viewData"
            :visible="detailsVisible"
            @closed="onCloseDetails"
        />

        <SubscriptionModuleVisibilityMessage moduleName="product" />

        <a-row :gutter="16">
            <a-col :span="24">
                <a-tabs v-model:activeKey="productType" @change="setUrlData">
                    <a-tab-pane key="single">
                        <template #tab>
                            <span>
                                <AppstoreAddOutlined />
                                {{ $t("variations.single_type_product") }}
                            </span>
                        </template>
                    </a-tab-pane>
                    <a-tab-pane key="variable">
                        <template #tab>
                            <span>
                                <ClusterOutlined />
                                {{ $t("variations.variant_type_product") }}
                            </span>
                        </template>
                    </a-tab-pane>
                </a-tabs>
            </a-col>
        </a-row>

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template
                                v-if="
                                    column.dataIndex === 'status' &&
                                    record &&
                                    record.details
                                "
                            >
                                <a-popover
                                    v-if="record.details.status == 'out_of_stock'"
                                    placement="top"
                                >
                                    <template #content>
                                        {{ $t("common.out_of_stock") }}
                                    </template>
                                    <a-badge status="error" />
                                </a-popover>
                                <a-badge v-else status="success" />
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                <a-badge>
                                    <a-avatar shape="square" :src="record.image_url" />
                                    {{ record.name }}
                                </a-badge>
                            </template>
                            <template v-if="column.dataIndex === 'warehouse_id'">
                                {{
                                    record.details &&
                                    record.details.warehouse &&
                                    record.details.warehouse.name
                                        ? record.details.warehouse.name
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'category_id'">
                                {{ record.category ? record.category.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'brand_id'">
                                {{ record.brand ? record.brand.name : "-" }}
                            </template>
                            <template v-if="column.dataIndex === 'sales_price'">
                                <span
                                    v-if="
                                        productType == 'single' &&
                                        record &&
                                        record.details
                                    "
                                >
                                    {{ formatAmountCurrency(record.details.sales_price) }}
                                </span>
                                <span v-else-if="productType == 'variable'">
                                    {{ getVariableProductSalePrice(record) }}
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'purchase_price'">
                                <span
                                    v-if="
                                        productType == 'single' &&
                                        record &&
                                        record.details
                                    "
                                >
                                    {{
                                        formatAmountCurrency(
                                            record.details.purchase_price
                                        )
                                    }}
                                </span>
                                <span v-else-if="productType == 'variable'">
                                    {{ getVariableProductPurchasePrice(record) }}
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'current_stock'">
                                <span
                                    v-if="
                                        productType == 'single' &&
                                        record &&
                                        record.details
                                    "
                                >
                                    {{
                                        `${record.details.current_stock} ${record.unit.short_name}`
                                    }}
                                </span>
                                <span v-else-if="productType == 'variable'">
                                    {{ getVariableProductStockSum(record) }}
                                </span>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-space>
                                    <a-button @click="viewItem(record)" type="primary">
                                        <template #icon><EyeOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('products_edit') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="editItem(record)"
                                    >
                                        <template #icon><EditOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            permsArray.includes('products_delete') ||
                                            permsArray.includes('admin')
                                        "
                                        type="primary"
                                        @click="showDeleteConfirm(record.xid)"
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                </a-space>
                            </template>
                        </template>
                        <template
                            v-if="productType == 'variable'"
                            #expandedRowRender="productData"
                        >
                            <a-table
                                v-if="
                                    productData &&
                                    productData.record &&
                                    productData.record.variations
                                "
                                :columns="variantColumns"
                                :row-key="(record) => record.xid"
                                :data-source="productData.record.variations"
                                :pagination="false"
                                size="middle"
                            >
                                <template #bodyCell="{ column, text, record }">
                                    <template v-if="column.dataIndex === 'name'">
                                        {{ record.name }}
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex === 'sales_price' &&
                                            record &&
                                            record.details
                                        "
                                    >
                                        {{
                                            formatAmountCurrency(
                                                record.details.sales_price
                                            )
                                        }}
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex === 'purchase_price' &&
                                            record &&
                                            record.details
                                        "
                                    >
                                        {{
                                            formatAmountCurrency(
                                                record.details.purchase_price
                                            )
                                        }}
                                    </template>
                                    <template
                                        v-if="
                                            column.dataIndex === 'current_stock' &&
                                            record &&
                                            record.details
                                        "
                                    >
                                        {{
                                            `${record.details.current_stock} ${productData.record.unit.short_name}`
                                        }}
                                    </template>
                                    <template v-if="column.dataIndex === 'action'">
                                        <a-button @click="viewItem(record)" type="links">
                                            <template #icon><EyeOutlined /></template>
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>
<script>
import { onMounted, ref, watch } from "vue";
import {
    EyeOutlined,
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ClusterOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";
import { maxBy, minBy, sumBy } from "lodash-es";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import ProductInfo from "../../../../common/components/product/ProductInfo.vue";
import AddEdit from "./AddEdit.vue";
import ProductView from "./View.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import ImportProducts from "../../../../common/core/ui/Import.vue";
import SubscriptionModuleVisibility from "../../../../common/components/common/visibility/SubscriptionModuleVisibility.vue";
import SubscriptionModuleVisibilityMessage from "../../../../common/components/common/visibility/SubscriptionModuleVisibilityMessage.vue";

export default {
    components: {
        EyeOutlined,
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ClusterOutlined,
        AppstoreAddOutlined,
        AddEdit,
        ProductView,
        ProductInfo,
        AdminPageHeader,
        ImportProducts,

        SubscriptionModuleVisibility,
        SubscriptionModuleVisibilityMessage,
    },
    setup() {
        const {
            addEditUrl,
            initData,
            columns,
            variantColumns,
            filterableColumns,
            hashableColumns,
            multiDimensalObjectColumns,
        } = fields();
        const crudVariables = crud();
        const {
            appSetting,
            permsArray,
            formatAmountCurrency,
            getRecursiveCategories,
            filterTreeNode,
            selectedWarehouse,
        } = common();
        const filters = ref({
            category_id: undefined,
            brand_id: undefined,
        });
        const sampleFileUrl = window.config.product_sample_file;

        const categories = ref([]);
        const brands = ref([]);
        const productType = ref("single");

        onMounted(() => {
            getInitialData();
            setUrlData();

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "product";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
            crudVariables.multiDimensalObjectColumns.value = {
                ...multiDimensalObjectColumns,
            };
        });

        const setUrlData = () => {
            var url =
                "products?fields=id,xid,name,slug,product_type,barcode_symbology,item_code,image,image_url,category_id,x_category_id,category{id,xid,name},brand_id,x_brand_id,brand{id,xid,name},unit_id,x_unit_id,unit{id,xid,name,short_name},description,details{stock_quantitiy_alert,opening_stock,opening_stock_date,wholesale_price,wholesale_quantity,mrp,purchase_price,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type,current_stock,warehouse_id,x_warehouse_id,status},details:tax{id,xid,name,rate},details:warehouse{id,xid,name},customFields{id,xid,field_name,field_value},warehouse_id,x_warehouse_id,warehouse{id,xid}";

            if (productType.value == "variable") {
                url =
                    "products?fields=id,xid,name,product_type,slug,barcode_symbology,item_code,image,image_url,category_id,x_category_id,category{id,xid,name},brand_id,x_brand_id,brand{id,xid,name},unit_id,x_unit_id,unit{id,xid,name,short_name},description,details{stock_quantitiy_alert,opening_stock,opening_stock_date,wholesale_price,wholesale_quantity,mrp,purchase_price,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type,current_stock,warehouse_id,x_warehouse_id,status},details:tax{id,xid,name,rate},details:warehouse{id,xid,name},customFields{id,xid,field_name,field_value},warehouse_id,x_warehouse_id,warehouse{id,xid},parent_id,x_parent_id,variations.limit(10000).offset(0){id,xid,name,slug,item_code,product_type,barcode_symbology,item_code,image,image_url,category_id,x_category_id,unit_id,x_unit_id,brand_id,x_brand_id},variations:productVariations{id,xid,product_id,x_product_id,variant_id,x_variant_id,variant_value_id,x_variant_value_id},variations:category{id,xid,name},variations:brand{id,xid,name},variations:unit{id,xid,name,short_name},variations:productVariations:variation{id,xid,name},variations:productVariations:variationType{id,xid,name},variations:details{stock_quantitiy_alert,opening_stock,opening_stock_date,wholesale_price,wholesale_quantity,mrp,purchase_price,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type,current_stock,warehouse_id,x_warehouse_id,status},variations:details:tax{id,xid,name,rate},variations:details:warehouse{id,xid,name}";
            }

            crudVariables.tableUrl.value = {
                url,
                filters,
                extraFilters: {
                    product_type: productType.value,
                },
            };

            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });
        };

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

        const getVariableProductSalePrice = (record) => {
            var priceString = "";
            const minRecord = minBy(record.variations, (o) => {
                return o.details.sales_price;
            });
            const maxRecord = maxBy(record.variations, (o) => {
                return o.details.sales_price;
            });

            if (minRecord && minRecord.details.sales_price) {
                priceString += formatAmountCurrency(minRecord.details.sales_price);
            }

            if (maxRecord && maxRecord.details.sales_price) {
                priceString +=
                    " - " + formatAmountCurrency(maxRecord.details.sales_price);
            }

            return priceString;
        };

        const getVariableProductPurchasePrice = (record) => {
            var priceString = "";
            const minRecord = minBy(record.variations, (o) => {
                return o.details.purchase_price;
            });
            const maxRecord = maxBy(record.variations, (o) => {
                return o.details.purchase_price;
            });

            if (minRecord && minRecord.details.sales_price) {
                priceString += formatAmountCurrency(minRecord.details.purchase_price);
            }

            if (maxRecord && maxRecord.details.sales_price) {
                priceString +=
                    " - " + formatAmountCurrency(maxRecord.details.purchase_price);
            }

            return priceString;
        };

        const getVariableProductStockSum = (record) => {
            return sumBy(record.variations, (o) => {
                return o.details.current_stock;
            });
        };

        watch(selectedWarehouse, (newVal, oldVal) => {
            setUrlData();
        });

        return {
            columns,
            variantColumns,
            appSetting,
            ...crudVariables,
            permsArray,
            formatAmountCurrency,

            categories,
            brands,
            filters,
            filterTreeNode,
            setUrlData,
            sampleFileUrl,
            productType,

            getVariableProductSalePrice,
            getVariableProductPurchasePrice,
            getVariableProductStockSum,
        };
    },
};
</script>

<style lang="less">
.ant-badge-status-dot {
    width: 8px;
    height: 8px;
}
</style>

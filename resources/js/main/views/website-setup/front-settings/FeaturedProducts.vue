<template>
    <a-card
        :title="$t('front_setting.featured_products')"
        class="page-content-container mt-20 mb-20"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.featured_products_title')"
                        name="featured_products_title"
                        :help="
                            rules.featured_products_title
                                ? rules.featured_products_title.message
                                : null
                        "
                        :validateStatus="rules.featured_products_title ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.featured_products_title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.title'),
                                ])
                            "
                        />
                    </a-form-item>

                    <a-form-item
                        :label="$t('front_setting.featured_products_subtitle')"
                        name="featured_products_subtitle"
                        :help="
                            rules.featured_products_subtitle
                                ? rules.featured_products_subtitle.message
                                : null
                        "
                        :validateStatus="
                            rules.featured_products_subtitle ? 'error' : null
                        "
                    >
                        <a-input
                            v-model:value="
                                addEditForm.formData.featured_products_subtitle
                            "
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.subtitle'),
                                ])
                            "
                        />
                    </a-form-item>

                    <a-form-item
                        :label="$t('product.product')"
                        name="featured_products"
                        :help="
                            rules.featured_products
                                ? rules.featured_products.message
                                : null
                        "
                        :validateStatus="rules.featured_products ? 'error' : null"
                    >
                        <a-select
                            v-model:value="searchTerm"
                            show-search
                            :filter-option="false"
                            :placeholder="
                                $t('common.select_default_text', [$t('product.product')])
                            "
                            style="width: 100%"
                            :not-found-content="productFetching ? undefined : null"
                            @search="fetchProducts"
                            option-label-prop="label"
                            @focus="products = []"
                            @select="searchValueSelected"
                        >
                            <template #suffixIcon><SearchOutlined /></template>
                            <template v-if="productFetching" #notFoundContent>
                                <a-spin size="small" />
                            </template>
                            <a-select-option
                                v-for="product in products"
                                :key="product.xid"
                                :value="product.xid"
                                :label="product.name"
                                :product="product"
                            >
                                => {{ product.name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-list item-layout="horizontal" :data-source="selectedProducts">
                        <template #renderItem="{ item }">
                            <a-list-item>
                                <a-list-item-meta>
                                    <template #title>
                                        {{ item.name }}
                                    </template>
                                    <template #description>
                                        <a-space>
                                            <div>
                                                <a-tag
                                                    v-if="item.category_id"
                                                    color="purple"
                                                >
                                                    {{ item.category.name }}
                                                </a-tag>
                                                <a-tag v-if="item.brand_id" color="cyan">
                                                    {{ item.brand.name }}
                                                </a-tag>
                                            </div>
                                        </a-space>
                                        <br />
                                        <a-space class="mt-5">
                                            <span>
                                                {{ $t("product.sales_price") }}:
                                            </span>
                                            <span>
                                                {{
                                                    formatAmountCurrency(
                                                        getSalesPriceWithTax(item)
                                                    )
                                                }}
                                            </span>
                                            <del>{{
                                                formatAmountCurrency(item.details.mrp)
                                            }}</del>
                                        </a-space>
                                    </template>
                                    <template #avatar>
                                        <a-avatar :src="item.image_url" />
                                    </template>
                                </a-list-item-meta>
                                <template #actions>
                                    <a-button type="link" @click="removeItem(item.xid)">
                                        <delete-outlined
                                            :style="{
                                                fontSize: '20px',
                                                color: '#f87171',
                                            }"
                                        />
                                    </a-button>
                                </template>
                            </a-list-item>
                        </template>
                    </a-list>
                </a-col>
            </a-row>

            <a-row :gutter="16" class="mt-30">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-button type="primary" @click="onSubmit">
                            <template #icon> <SaveOutlined /> </template>
                            {{ $t("common.update") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
    </a-card>
</template>

<script>
import { defineComponent, reactive, ref, toRefs, onMounted, computed, watch } from "vue";
import { SaveOutlined, SearchOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { debounce } from "lodash-es";
import common from "../../../../common/composable/common";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";
import { filter } from "lodash-es";

export default defineComponent({
    props: ["formData", "data", "rules"],
    emits: ["onSubmit"],
    components: {
        SaveOutlined,
        SearchOutlined,
        DeleteOutlined,
    },
    setup(props, { emit }) {
        const state = reactive({
            searchTerm: [],
            productFetching: false,
            products: [],
        });
        const selectedProducts = ref([]);
        const { formatAmountCurrency } = common();
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });

        onMounted(() => {
            addEditForm.formData = props.formData;
            selectedProducts.value = props.data.featured_products_details;
        });

        const fetchProducts = debounce((value) => {
            state.products = [];

            if (value != "") {
                state.productFetching = true;
                const featuredProducsArray = addEditForm.formData.featured_products;
                let url =
                    "products?fields=id,xid,name,image,image_url,details{id,xid,product_id,x_product_id,sales_price,sales_tax_type,tax_id,x_tax_id,mrp},brand_id,x_brand_id,category_id,x_category_id,details:tax{id,xid,rate},brand{id,xid,name,image,image_url},category{id,xid,name,image,image_url}";
                let filterString = "";
                let hashableString = "";

                if (featuredProducsArray.length > 0) {
                    let newString = "";
                    featuredProducsArray.forEach((element) => {
                        newString += `products.id ne ${element} and `;
                        hashableString += `${element},`;
                    });

                    filterString += `(${newString.substring(
                        0,
                        newString.length - 5
                    )}) and `;

                    hashableString = hashableString.substring(
                        0,
                        hashableString.length - 1
                    );
                }

                filterString += `products.name lk "%${value}%"`;
                url += `&filters=${encodeURIComponent(filterString)}`;
                url += `&hashable=${hashableString}`;

                axiosAdmin.get(url).then((response) => {
                    state.products = response.data;
                    state.productFetching = false;
                });
            }
        }, 300);

        const searchValueSelected = (value, option) => {
            const newProduct = option.product;
            selectedProducts.value = [...selectedProducts.value, newProduct];
            addEditForm.formData.featured_products = [
                ...addEditForm.formData.featured_products,
                newProduct.xid,
            ];

            state.searchTerm = [];
            state.products = [];
        };

        const removeItem = (productId) => {
            const newPoductLists = filter(
                selectedProducts.value,
                (product) => product.xid != productId
            );
            const newPoductListsIds = filter(
                addEditForm.formData.featured_products,
                (product) => product != productId
            );

            selectedProducts.value = newPoductLists;
            addEditForm.formData.featured_products = newPoductListsIds;
        };

        const onSubmit = () => {
            emit("onSubmit", addEditForm.formData);
        };

        return {
            ...toRefs(state),
            fetchProducts,
            searchValueSelected,
            selectedProducts,
            formatAmountCurrency,
            getSalesPriceWithTax,
            removeItem,

            onSubmit,
            addEditForm,
        };
    },
});
</script>

<style></style>

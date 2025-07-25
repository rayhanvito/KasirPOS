<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('product_card.title')"
                        name="title"
                        :help="rules.title ? rules.title.message : null"
                        :validateStatus="rules.title ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.title'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('product_card.subtitle')"
                        name="subtitle"
                        :help="rules.subtitle ? rules.subtitle.message : null"
                        :validateStatus="rules.subtitle ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.subtitle"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.subtitle'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('product_card.products')"
                        name="products"
                        :help="rules.products ? rules.products.message : null"
                        :validateStatus="rules.products ? 'error' : null"
                    >
                        <a-select
                            v-model:value="orderSearchTerm"
                            show-search
                            :filter-option="false"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('product_card.products'),
                                ])
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
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
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
        </a-form>
        <template #footer>
            <a-button
                key="submit"
                type="primary"
                :loading="addEditForm.formSubmitting"
                @click="onSubmit"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, reactive, ref, toRefs, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SearchOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { notification, message } from "ant-design-vue";
import { debounce, filter } from "lodash-es";
import processRequest from "../../../../common/plugins/processRequest";
import common from "../../../../common/composable/common";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default defineComponent({
    props: ["formData", "data", "visible", "url", "addEditType"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SearchOutlined,
        DeleteOutlined,
    },
    setup(props, { emit }) {
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });
        const rules = ref({});
        const onClose = () => {
            rules.value = {};
            emit("closed");
        };
        const { t } = useI18n();
        const state = reactive({
            orderSearchTerm: [],
            productFetching: false,
            products: [],
        });
        const selectedProducts = ref([]);
        const { formatAmountCurrency } = common();

        const pageTitle = computed(() => {
            return props.addEditType == "add"
                ? t("product_card.add")
                : t("product_card.edit");
        });

        const onSubmit = () => {
            addEditForm.formSubmitting = true;

            processRequest({
                url: props.url,
                data: addEditForm.formData,
                success: (res) => {
                    // Toastr Notificaiton
                    notification.success({
                        placement: "bottomRight",
                        message: t("common.success"),
                        description:
                            props.addEditType == "add"
                                ? t("product_card.created")
                                : t("product_card.updated"),
                    });

                    emit("addEditSuccess", res.xid);
                    addEditForm.formSubmitting = false;
                    rules.value = {};
                },
                error: (errorRules) => {
                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                    addEditForm.formSubmitting = false;
                },
            });
        };

        const fetchProducts = debounce((value) => {
            state.products = [];

            if (value != "") {
                state.productFetching = true;
                let url = "product-lists/search-products";

                axiosAdmin
                    .post(url, {
                        search_term: value,
                        except: addEditForm.formData.products,
                    })
                    .then((response) => {
                        state.products = response.data.products;
                        state.productFetching = false;
                    });
            }
        }, 300);

        const searchValueSelected = (value, option) => {
            const newProduct = option.product;

            selectedProducts.value = [...selectedProducts.value, newProduct];
            addEditForm.formData.products = [
                ...addEditForm.formData.products,
                newProduct.xid,
            ];

            state.orderSearchTerm = [];
            state.products = [];
        };

        const removeItem = (productId) => {
            const newPoductLists = filter(
                selectedProducts.value,
                (product) => product.xid != productId
            );
            const newPoductListsIds = filter(
                addEditForm.formData.products,
                (product) => product != productId
            );

            selectedProducts.value = newPoductLists;
            addEditForm.formData.products = newPoductListsIds;
        };

        watch(props, (newVal, oldVal) => {
            addEditForm.formData = { ...newVal.formData };
            selectedProducts.value =
                newVal.addEditType == "edit" ? newVal.data.products_details : [];
        });

        return {
            ...toRefs(state),
            fetchProducts,
            searchValueSelected,
            selectedProducts,

            rules,
            onClose,
            onSubmit,
            pageTitle,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            formatAmountCurrency,
            getSalesPriceWithTax,
            removeItem,

            addEditForm,
        };
    },
});
</script>

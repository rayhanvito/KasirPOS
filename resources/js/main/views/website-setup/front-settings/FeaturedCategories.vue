<template>
    <a-card
        :title="$t('front_setting.featured_categories')"
        class="page-content-container mt-20 mb-20 mt-20 mb-20"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.featured_categories_title')"
                        name="featured_categories_title"
                        :help="
                            rules.featured_categories_title
                                ? rules.featured_categories_title.message
                                : null
                        "
                        :validateStatus="rules.featured_categories_title ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.featured_categories_title"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.title'),
                                ])
                            "
                        />
                    </a-form-item>

                    <a-form-item
                        :label="$t('front_setting.featured_categories_subtitle')"
                        name="featured_categories_subtitle"
                        :help="
                            rules.featured_categories_subtitle
                                ? rules.featured_categories_subtitle.message
                                : null
                        "
                        :validateStatus="
                            rules.featured_categories_subtitle ? 'error' : null
                        "
                    >
                        <a-input
                            v-model:value="
                                addEditForm.formData.featured_categories_subtitle
                            "
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product_card.subtitle'),
                                ])
                            "
                        />
                    </a-form-item>

                    <a-form-item
                        :label="$t('category.category')"
                        name="featured_categories"
                        :help="
                            rules.featured_categories
                                ? rules.featured_categories.message
                                : null
                        "
                        :validateStatus="rules.featured_categories ? 'error' : null"
                    >
                        <a-select
                            v-model:value="searchTerm"
                            show-search
                            :filter-option="false"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('category.category'),
                                ])
                            "
                            style="width: 100%"
                            :not-found-content="categoryFetching ? undefined : null"
                            @search="fetchCategories"
                            option-label-prop="label"
                            @focus="categories = []"
                            @select="searchValueSelected"
                        >
                            <template #suffixIcon><SearchOutlined /></template>
                            <template v-if="categoryFetching" #notFoundContent>
                                <a-spin size="small" />
                            </template>
                            <a-select-option
                                v-for="category in categories"
                                :key="category.xid"
                                :value="category.xid"
                                :label="category.name"
                                :category="category"
                            >
                                {{ category.name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-list
                        item-layout="horizontal"
                        :data-source="selectedCategoriess"
                        size="middle"
                    >
                        <template #renderItem="{ item }">
                            <a-list-item>
                                <a-list-item-meta>
                                    <template #title>
                                        {{ item.name }}
                                    </template>
                                    <template #avatar>
                                        <a-avatar :src="item.image_url" size="small" />
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
            categoryFetching: false,
            categories: [],
        });
        const selectedCategoriess = ref([]);
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });

        onMounted(() => {
            addEditForm.formData = props.formData;
            selectedCategoriess.value = props.data.featured_categories_details;
        });

        const fetchCategories = debounce((value) => {
            state.categories = [];

            if (value != "") {
                state.categoryFetching = true;
                const featuredCategoriesArray = addEditForm.formData.featured_categories;
                let url = "categories?fields=id,xid,name,image,image_url";
                let filterString = "";
                let hashable = "";

                if (featuredCategoriesArray.length > 0) {
                    let newString = "";
                    featuredCategoriesArray.forEach((element) => {
                        newString += `id ne ${element} and `;
                        hashable += `${element},`;
                    });

                    filterString += `(${newString.substring(
                        0,
                        newString.length - 5
                    )}) and `;
                }

                filterString += `name lk "%${value}%"`;
                url += `&filters=${encodeURIComponent(filterString)}`;
                url += `&hashable=${hashable}`;

                axiosAdmin.get(url).then((response) => {
                    state.categories = response.data;
                    state.categoryFetching = false;
                });
            }
        }, 300);

        const searchValueSelected = (value, option) => {
            const newCategory = option.category;
            selectedCategoriess.value = [...selectedCategoriess.value, newCategory];
            addEditForm.formData.featured_categories = [
                ...addEditForm.formData.featured_categories,
                newCategory.xid,
            ];

            state.searchTerm = [];
            state.categories = [];
        };

        const removeItem = (categoryId) => {
            const newCategoryLists = filter(
                selectedCategoriess.value,
                (category) => category.xid != categoryId
            );
            const newCategoryListsIds = filter(
                addEditForm.formData.featured_categories,
                (category) => category != categoryId
            );

            selectedCategoriess.value = newCategoryLists;
            addEditForm.formData.featured_categories = newCategoryListsIds;
        };

        const onSubmit = () => {
            emit("onSubmit", addEditForm.formData);
        };

        return {
            ...toRefs(state),
            fetchCategories,
            searchValueSelected,
            selectedCategoriess,
            removeItem,
            addEditForm,

            onSubmit,
        };
    },
});
</script>

<style></style>

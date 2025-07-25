<template>
    <div class="bg-white">
        <a-row type="flex" justify="center">
            <a-col :span="20">
                <a-row :gutter="[20, 30]" class="mt-20">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <a-carousel autoplay>
                            <div
                                v-for="item in frontSettings.bottom_banners_1_details"
                                :key="item.uid"
                            >
                                <img :src="item.url" :style="{ width: '100%' }" />
                            </div>
                        </a-carousel>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <a-row :gutter="[20, 20]">
                            <a-col :span="24">
                                <a-carousel autoplay>
                                    <div
                                        v-for="item in frontSettings.bottom_banners_2_details"
                                        :key="item.uid"
                                    >
                                        <img :src="item.url" :style="{ width: '100%' }" />
                                    </div>
                                </a-carousel>
                            </a-col>
                            <a-col :span="24">
                                <div
                                    v-for="item in frontSettings.bottom_banners_3_details"
                                    :key="item.uid"
                                >
                                    <img :src="item.url" :style="{ width: '100%' }" />
                                </div>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>

                <a-divider
                    v-if="
                        (frontSettings.bottom_banners_1_details &&
                            frontSettings.bottom_banners_1_details.length > 0) ||
                        (frontSettings.bottom_banners_2_details &&
                            frontSettings.bottom_banners_2_details.length > 0) ||
                        (frontSettings.bottom_banners_3_details &&
                            frontSettings.bottom_banners_3_details.length > 0)
                    "
                    dashed
                />

                <div
                    v-if="
                        frontSettings.featured_products_details &&
                        frontSettings.featured_products_details.length > 0
                    "
                >
                    <div style="text-align: center" class="mt-50 mb-40">
                        <a-typography-title :level="3" :style="{ marginBottom: '5px' }">
                            {{ frontSettings.featured_products_title }}
                        </a-typography-title>
                        <a-typography-title
                            v-if="frontSettings.featured_products_subtitle"
                            type="secondary"
                            :level="5"
                            :style="{ marginTop: '0px' }"
                        >
                            {{ frontSettings.featured_products_subtitle }}
                        </a-typography-title>
                    </div>

                    <div class="prdoct-card-list-body mt-20 mb-50">
                        <a-row :gutter="[30, 30]">
                            <a-col
                                v-for="product in featuredProducts"
                                :xs="24"
                                :sm="12"
                                :md="8"
                                :lg="6"
                                :xl="4"
                                :key="product.id"
                            >
                                <ProductCard
                                    :product="product"
                                    :key="product.id"
                                    :currency="currency"
                                />
                            </a-col>
                        </a-row>
                    </div>
                    <a-divider dashed />
                </div>

                <div
                    v-if="
                        frontSettings.featured_categories_details &&
                        frontSettings.featured_categories_details.length > 0
                    "
                >
                    <div style="text-align: center" class="mt-50 mb-40">
                        <a-typography-title :level="3" :style="{ marginBottom: '5px' }">
                            {{ frontSettings.featured_categories_title }}
                        </a-typography-title>
                        <a-typography-title
                            v-if="frontSettings.featured_categories_subtitle"
                            type="secondary"
                            :level="5"
                            :style="{ marginTop: '0px' }"
                        >
                            {{ frontSettings.featured_categories_subtitle }}
                        </a-typography-title>
                    </div>
                    <a-row :gutter="[10, 10]" class="mt-20 mb-50 featured-categories">
                        <a-col
                            :xs="24"
                            :sm="12"
                            :md="8"
                            :lg="4"
                            :xl="4"
                            v-for="featuredCategory in frontSettings.featured_categories_details"
                            :key="featuredCategory.id"
                        >
                            <div
                                style="
                                    padding: 0px 10px 0px 10px;
                                    border-radius: 10px;
                                    border: 1px solid #eee;
                                "
                            >
                                <a-list
                                    item-layout="horizontal"
                                    :data-source="[featuredCategory]"
                                >
                                    <template #renderItem="{ item }">
                                        <a-list-item>
                                            <a-list-item-meta>
                                                <template #title>
                                                    {{ item.name }}
                                                </template>
                                                <template #avatar>
                                                    <a-avatar :src="item.image_url" />
                                                </template>
                                            </a-list-item-meta>
                                        </a-list-item>
                                    </template>
                                </a-list>
                            </div>
                        </a-col>
                    </a-row>
                    <a-divider dashed />
                </div>

                <div v-if="frontProductCards && frontProductCards.length > 0">
                    <div
                        v-for="frontProductCard in frontProductCards"
                        :key="frontProductCard.id"
                        class="prdoct-card-list"
                    >
                        <div style="text-align: center" class="mt-50 mb-40">
                            <a-typography-title
                                :level="3"
                                :style="{ marginBottom: '5px' }"
                            >
                                {{ frontProductCard.title }}
                            </a-typography-title>
                            <a-typography-title
                                v-if="frontProductCard.subtitle != ''"
                                type="secondary"
                                :level="5"
                                :style="{ marginTop: '0px' }"
                            >
                                {{ frontProductCard.subtitle }}
                            </a-typography-title>
                        </div>

                        <div class="prdoct-card-list-body mt-20 mb-50">
                            <a-row :gutter="[30, 30]">
                                <a-col
                                    v-for="product in frontProductCard.products_details"
                                    :xs="24"
                                    :sm="12"
                                    :md="8"
                                    :lg="6"
                                    :xl="4"
                                    :key="product.id"
                                >
                                    <ProductCard
                                        :product="product"
                                        :key="product.xid"
                                        :currency="currency"
                                    />
                                </a-col>
                            </a-row>
                        </div>
                        <a-divider dashed />
                    </div>
                </div>

                <a-row class="mt-20 mb-20">
                    <a-col :span="24">
                        <a-carousel arrows>
                            <template #prevArrow>
                                <div
                                    class="custom-slick-arrow"
                                    style="left: 10px; zindex: 1"
                                >
                                    <left-circle-outlined />
                                </div>
                            </template>
                            <template #nextArrow>
                                <div class="custom-slick-arrow" style="right: 10px">
                                    <RightCircleOutlined />
                                </div>
                            </template>
                            <div
                                v-for="item in frontSettings.top_banners_details"
                                :key="item.uid"
                            >
                                <img :src="item.url" :style="{ width: '100%' }" />
                            </div>
                        </a-carousel>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    RightOutlined,
    RightCircleOutlined,
    LeftCircleOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import ProductCard from "./components/ProductCard.vue";
import CategoryHeader from "./includes/CategoryHeader.vue";
import common from "../../../common/composable/common";

export default defineComponent({
    components: {
        RightOutlined,
        RightCircleOutlined,
        LeftCircleOutlined,
        ProductCard,
        CategoryHeader,
    },
    setup() {
        const { frontWarehouse } = common();
        const route = useRoute();
        const frontSettings = ref({});
        const frontProductCards = ref([]);
        const featuredProducts = ref([]);
        const currency = ref({});

        onMounted(() => {
            axiosFront
                .get(`front/homepage/${frontWarehouse.value.slug}`)
                .then((response) => {
                    currency.value = response.data.currency;
                    frontSettings.value = response.data.front_settings;
                    frontProductCards.value = response.data.front_product_cards;
                    featuredProducts.value =
                        frontSettings.value.featured_products_details;
                });
        });

        return {
            frontSettings,
            frontProductCards,
            featuredProducts,
            currency,
        };
    },
});
</script>

<style lang="less">
.prdoct-card-list {
    margin-bottom: 20px;
    margin-top: 20px;
    border-radius: 10px;
}

.prdoct-card-list-body {
    padding: 10px 0px 20px 0px;
}

.featured-categories .ant-list-item-meta-title {
    margin-top: 6px;
}
</style>

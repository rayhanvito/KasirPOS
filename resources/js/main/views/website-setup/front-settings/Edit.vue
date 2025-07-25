<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.front_settings`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.website_setup") }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.front_settings") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <div class="setting-sidebar">
                <perfect-scrollbar
                    :options="{
                        wheelSpeed: 1,
                        swipeEasing: true,
                        suppressScrollX: true,
                    }"
                >
                    <a-menu v-model:selectedKeys="activeKey">
                        <a-menu-item key="featured_categories">
                            {{ $t("front_setting.featured_categories") }}
                        </a-menu-item>
                        <a-menu-item key="featured_producs">
                            {{ $t("front_setting.featured_products") }}
                        </a-menu-item>
                        <a-menu-item key="social_links">
                            {{ $t("front_setting.social_links") }}
                        </a-menu-item>
                        <a-menu-item key="footers">
                            {{ $t("front_setting.footers") }}
                        </a-menu-item>
                        <a-menu-item key="banners">
                            {{ $t("front_setting.banners") }}
                        </a-menu-item>
                    </a-menu>
                </perfect-scrollbar>
            </div>
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <template v-if="formData && formData.featured_categories">
                    <FeaturedCategories
                        v-show="activeKey[0] == 'featured_categories'"
                        :formData="formData"
                        :data="frontSettingsData"
                        :rules="rules"
                        @onSubmit="onSubmit"
                    />
                    <FeaturedProducts
                        v-show="activeKey[0] == 'featured_producs'"
                        :formData="formData"
                        :data="frontSettingsData"
                        :rules="rules"
                        @onSubmit="onSubmit"
                    />
                    <SocialLinks
                        v-show="activeKey[0] == 'social_links'"
                        :formData="formData"
                        :data="frontSettingsData"
                        :rules="rules"
                        @onSubmit="onSubmit"
                    />
                    <Footers
                        v-show="activeKey[0] == 'footers'"
                        :formData="formData"
                        :data="frontSettingsData"
                        :rules="rules"
                        @onSubmit="onSubmit"
                    />
                    <Banners
                        v-show="activeKey[0] == 'banners'"
                        :formData="formData"
                        :data="frontSettingsData"
                        :rules="rules"
                        @onSubmit="onSubmit"
                    />
                </template>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { notification } from "ant-design-vue";
import {
    HomeOutlined,
    ShoppingOutlined,
    SettingOutlined,
    LogoutOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import processRequest from "../../../../common/plugins/processRequest";
import FeaturedProducts from "./FeaturedProducts.vue";
import FeaturedCategories from "./FeaturedCategories.vue";
import SocialLinks from "./SocialLinks.vue";
import Footers from "./Footers.vue";
import Banners from "./Banners.vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

export default defineComponent({
    components: {
        FeaturedCategories,
        FeaturedProducts,
        SocialLinks,
        Footers,
        Banners,

        HomeOutlined,
        ShoppingOutlined,
        SettingOutlined,
        LogoutOutlined,
        SaveOutlined,

        AdminPageHeader,
    },
    setup() {
        const { selectedWarehouse } = common();
        const { t } = useI18n();
        const rules = ref({});
        const formData = ref({});
        const frontSettingsData = ref({});
        const activeKey = ref(["featured_categories"]);

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            const url =
                "front-settings?fields=id,xid,featured_categories,x_featured_categories,featured_products_details,featured_categories_title,featured_categories_subtitle,featured_products,x_featured_products,featured_categories_details,featured_products_title,featured_products_subtitle,facebook_url,twitter_url,instagram_url,linkedin_url,youtube_url,pages_widget,contact_info_widget,links_widget,footer_copyright_text,top_banners,top_banners_details,bottom_banners_1,bottom_banners_1_details,bottom_banners_2,bottom_banners_2_details,bottom_banners_3,bottom_banners_3_details,footer_company_description";

            axiosAdmin.get(url).then((response) => {
                const responseData = response.data[0];
                formData.value = {
                    featured_categories: responseData.x_featured_categories,
                    featured_categories_title: responseData.featured_categories_title,
                    featured_categories_subtitle:
                        responseData.featured_categories_subtitle,
                    featured_products: responseData.x_featured_products,
                    featured_products_title: responseData.featured_products_title,
                    featured_products_subtitle: responseData.featured_products_subtitle,
                    facebook_url: responseData.facebook_url,
                    twitter_url: responseData.twitter_url,
                    instagram_url: responseData.instagram_url,
                    linkedin_url: responseData.linkedin_url,
                    youtube_url: responseData.youtube_url,
                    pages_widget: responseData.pages_widget,
                    contact_info_widget: responseData.contact_info_widget,
                    links_widget: responseData.links_widget,
                    footer_company_description: responseData.footer_company_description,
                    footer_copyright_text: responseData.footer_copyright_text,
                    top_banners: responseData.top_banners,
                    bottom_banners_1: responseData.bottom_banners_1,
                    bottom_banners_2: responseData.bottom_banners_2,
                    bottom_banners_3: responseData.bottom_banners_3,
                };
                frontSettingsData.value = responseData;
            });
        };

        const onSubmit = (formDataObject) => {
            processRequest({
                url: `front-settings/${frontSettingsData.value.xid}`,
                data: {
                    ...formDataObject,
                    _method: "PUT",
                },
                success: (res) => {
                    // Toastr Notificaiton
                    notification.success({
                        placement: "bottomRight",
                        message: t("common.success"),
                        description: t("front.setting_updated_successfully"),
                    });

                    rules.value = {};
                },
                error: (errorRules) => {
                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                },
            });
        };

        watch(selectedWarehouse, (newVal, oldVal) => {
            formData.value = {};
            frontSettingsData.value = {};

            setUrlData();
        });

        return {
            rules,
            formData,
            frontSettingsData,
            onSubmit,
            activeKey,
        };
    },
});
</script>

<style lang="less">
.border-right-none {
    border-right: 0px;
}
</style>

<template>
    <a-layout v-if="frontWarehouse.online_store_enabled == 1">
        <a-layout-header :style="{ background: '#2874f0', padding: 0 }">
            <a-row type="flex" justify="center">
                <a-col :span="20">
                    <a-row type="flex" justify="space-between">
                        <a-col
                            :xs="12"
                            :sm="12"
                            :md="innerWidth >= 768 ? 6 : 12"
                            :lg="4"
                            :xl="4"
                            v-if="frontWarehouse && frontWarehouse.slug"
                        >
                            <LeftSidebar />

                            <router-link
                                :to="{
                                    name: 'front.homepage',
                                    params: { warehouse: frontWarehouse.slug },
                                }"
                            >
                                <img
                                    :style="{
                                        width: innerWidth >= 768 ? '150px' : '110px',
                                    }"
                                    :src="frontWarehouse.dark_logo_url"
                                />
                            </router-link>
                        </a-col>
                        <a-col v-if="innerWidth >= 768" :md="12" :lg="12" :xl="12">
                        </a-col>
                        <a-col
                            :xs="12"
                            :sm="12"
                            :md="innerWidth >= 768 ? 6 : 12"
                            :lg="8"
                            :xl="8"
                        >
                            <div :style="{ textAlign: 'right' }">
                                <CheckoutDrawer @openLoginModal="openLoginModal" />
                                <Login
                                    :modalVisible="loginModalVisible"
                                    @modalClosed="loginModalClosed"
                                />
                            </div>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </a-layout-header>
        <a-layout-content>
            <div>
                <div :style="{ background: '#fff' }" class="subheader">
                    <a-row type="flex" justify="center">
                        <a-col :span="20">
                            <a-row>
                                <div class="subheader-menu-lists">
                                    <a-space
                                        v-if="
                                            frontAppSetting &&
                                            frontAppSetting.pages_widget
                                        "
                                    >
                                        <!-- <a-dropdown
											class="subheader-menu"
											overlayClassName="top-dropdown-box"
										>
											<AppstoreOutlined
												:style="{
													fontSize: '24px',
													verticalAlign: 'middle',
												}"
											/>
											<template #overlay>
												<LeftSidebarMenu />
											</template>
										</a-dropdown> -->
                                        <a
                                            v-for="(
                                                item, index
                                            ) in frontAppSetting.pages_widget"
                                            :key="index"
                                            class="subheader-menu ml-25"
                                            :href="item.value"
                                            target="_blank"
                                        >
                                            {{ item.title }}
                                        </a>
                                    </a-space>
                                </div>
                            </a-row>
                        </a-col>
                    </a-row>
                </div>

                <router-view></router-view>
                <Footer />
            </div>
        </a-layout-content>
    </a-layout>
    <div v-else class="no-online-store-container">
        <a-result
            status="warning"
            :title="$t('warehouse.no_online_store_exists')"
        ></a-result>
    </div>
</template>
<script>
import { defineComponent, ref } from "vue";
import { useStore } from "vuex";
import { DownOutlined, MenuOutlined, AppstoreOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import ProductCard from "../components/ProductCard.vue";
import Footer from "./Footer.vue";
import CheckoutDrawer from "../components/CheckoutDrawer.vue";
import Login from "../components/Login.vue";
import LeftSidebar from "./LeftSidebar.vue";
import LeftSidebarMenu from "./LeftSidebarMenu.vue";

export default defineComponent({
    components: {
        DownOutlined,
        MenuOutlined,
        AppstoreOutlined,
        ProductCard,
        Footer,
        CheckoutDrawer,
        Login,
        LeftSidebar,
        LeftSidebarMenu,
    },
    setup() {
        const store = useStore();
        const { frontWarehouse, frontAppSetting } = common();
        const searchValue = ref("");
        const loginModalVisible = ref(false);

        const openLoginModal = () => {
            loginModalVisible.value = true;
        };

        const loginModalClosed = () => {
            loginModalVisible.value = false;
        };

        return {
            frontAppSetting,
            searchValue,
            openLoginModal,
            loginModalClosed,
            loginModalVisible,
            frontWarehouse,

            innerWidth: window.innerWidth,

            openKeys: ref([]),
            selectedKeys: ref([]),
        };
    },
});
</script>
<style lang="less" scoped>
.ant-carousel :deep(.slick-arrow.custom-slick-arrow) {
    width: 25px;
    height: 25px;
    font-size: 25px;
    color: #fff;
    background-color: rgba(31, 45, 61, 0.11);
    opacity: 0.3;
    z-index: 1;
}
.ant-carousel :deep(.custom-slick-arrow:before) {
    display: none;
}
.ant-carousel :deep(.custom-slick-arrow:hover) {
    opacity: 0.5;
}

.subheader {
    border-bottom: 1px solid #e5e7eb;

    .subheader-menu-lists {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .subheader-menu {
        font-size: 16px;
        font-weight: 500;
        color: rgba(0, 0, 0, 0.85);
    }
}

.top-dropdown-box {
    .ant-dropdown-content {
        margin-top: 50px;
    }
}

.no-online-store-container {
    height: 100%;
    width: 100%;
    display: flex;
    position: fixed;
    align-items: center;
    justify-content: center;
    background: #f8f8ff;
}
</style>

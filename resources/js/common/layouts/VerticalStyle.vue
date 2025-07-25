<template>
    <Div>
        <section id="components-layout-demo-responsive">
            <a-layout>
                <!-- Sidebar untuk desktop/laptop -->
                <LeftSidebarBar v-if="innerWidth > 991" />

                <!-- Drawer untuk mobile/tablet -->
                <a-drawer
                    v-if="innerWidth <= 991"
                    placement="left"
                    :closable="false"
                    :open="!menuCollapsed"
                    :width="240"
                    :bodyStyle="{ padding: '0' }"
                    @close="store.commit('auth/updateMenuCollapsed', true)"
                >
                    <LeftSidebarBar />
                </a-drawer>

                <a-layout>
                    <MainArea
                        :innerWidth="innerWidth"
                        :collapsed="menuCollapsed"
                        :isRtl="appSetting.rtl"
                    >
                        <TopBar />
                        <MainContentArea>
                            <LicenseDetails v-if="appType == 'saas'" />

                            <a-layout-content>
                                <router-view></router-view>
                            </a-layout-content>

                            <AffixButton
                                v-if="
                                    appSetting.shortcut_menus != 'top' &&
                                    selectedWarehouse &&
                                    selectedWarehouse.name
                                "
                            />
                        </MainContentArea>
                    </MainArea>
                </a-layout>
            </a-layout>
        </section>
    </Div>
</template>

<script>
import { ref, onMounted, onUnmounted } from "vue";
import TopBar from "./TopBar.vue";
import LeftSidebarBar from "./LeftSidebar.vue";
import { Div, MainArea, MainContentArea } from "./style";
import common from "../composable/common";
import AffixButton from "./AffixButton.vue";
import LicenseDetails from "./LicenseDetails.vue";
import { useStore } from "vuex";

export default {
    components: {
        TopBar,
        LeftSidebarBar,
        Div,
        MainArea,
        MainContentArea,

        AffixButton,
        LicenseDetails,
    },
    setup() {
        const { appSetting, menuCollapsed, selectedWarehouse, appType } = common();
        const store = useStore();
        const innerWidth = ref(window.innerWidth);

        const handleResize = () => {
            innerWidth.value = window.innerWidth;
            // Jika ukuran layar berubah menjadi lebih besar dari 991px dan menu terbuka, tutup menu
            if (innerWidth.value > 991 && !menuCollapsed.value) {
                store.commit("auth/updateMenuCollapsed", true);
            }
        };

        onMounted(() => {
            window.addEventListener('resize', handleResize);
        });

        onUnmounted(() => {
            window.removeEventListener('resize', handleResize);
        });

        return {
            appType,
            appSetting,
            menuCollapsed,
            selectedWarehouse,
            innerWidth,
            store, // Make store available in template
        };
    },
};
</script>

<style>
#components-layout-demo-responsive .logo {
    height: 32px;
    margin: 16px;
    text-align: center;
}

.site-layout-sub-header-background {
    background: #fff;
}

.site-layout-background {
    background: #fff;
}

[data-theme="dark"] .site-layout-sub-header-background {
    background: #141414;
}
</style>
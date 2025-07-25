<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.update_app`)" class="p-0">
                <template #extra>
                    <InstallApp
                        v-if="settingRef"
                        :productStatus="settingRef.productStatus"
                        :product="settingRef.product.product"
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
                    {{ $t("menu.settings") }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.update_app") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <UpdateAppSettings ref="settingRef" />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UpdateAppSettings from "../../common/settings/update-app/index.vue";
import InstallApp from "../../common/settings/update-app/InstallApp.vue";

export default {
    components: {
        SettingSidebar,
        AdminPageHeader,
        UpdateAppSettings,
        InstallApp,
    },
    setup() {
        const settingRef = ref(null);

        return {
            settingRef,
        };
    },
};
</script>

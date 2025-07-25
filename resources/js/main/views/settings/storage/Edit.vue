<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.storage_settings`)" class="p-0">
                <template #extra>
                    <a-button
                        type="primary"
                        @click="onSubmit"
                        :loading="settingRef && settingRef.loading ? true : false"
                    >
                        <template #icon> <SaveOutlined /> </template>
                        {{ $t("common.update") }}
                    </a-button>
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
                    {{ $t("menu.storage_settings") }}
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
                <a-card class="page-content-container mt-20 mb-20">
                    <StorageSettings ref="settingRef" />
                </a-card>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import StorageSettings from "../../common/settings/storage/Edit.vue";

export default {
    components: {
        SettingSidebar,
        AdminPageHeader,
        StorageSettings,
        SaveOutlined,
    },
    setup() {
        const settingRef = ref(null);

        const onSubmit = () => {
            settingRef.value.onSubmit();
        };

        return {
            onSubmit,
            settingRef,
        };
    },
};
</script>

<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.currencies`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.settings`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.currencies`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <CurrencySettings ref="currencySettingRef">
                <template #actionButtons>
                    <template
                        v-if="
                            permsArray.includes('currencies_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("currency.add") }}
                        </a-button>
                    </template>
                </template>
            </CurrencySettings>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import CurrencySettings from "../../common/settings/currency/index.vue";

export default {
    components: {
        PlusOutlined,
        SettingSidebar,
        AdminPageHeader,
        CurrencySettings,
    },
    setup() {
        const { permsArray } = common();
        const currencySettingRef = ref(null);

        const addItem = () => {
            currencySettingRef.value.addItem();
        };

        return {
            permsArray,
            currencySettingRef,
            addItem,
        };
    },
};
</script>

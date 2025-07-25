<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                :title="$t(`menu.languages`)"
                @back="
                    () =>
                        $router.push({
                            name: 'admin.settings.translations.index',
                        })
                "
                class="p-0"
            />
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
                    <router-link :to="{ name: 'admin.settings.translations.index' }">
                        {{ $t(`menu.translations`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.languages`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <LanguageSettings ref="langSettingRef">
                <template #actionButtons>
                    <template
                        v-if="
                            permsArray.includes('translations_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-space>
                            <a-button type="primary" @click="addItem">
                                <PlusOutlined />
                                {{ $t("langs.add") }}
                            </a-button>
                            <ImportTranslation
                                v-if="appType == 'non-saas'"
                                :pageTitle="$t('translations.import_translations')"
                                :sampleFileUrl="sampleFileUrl"
                                importUrl="translations/import"
                            />
                        </a-space>
                    </template>
                </template>
            </LanguageSettings>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import common from "../../../../../common/composable/common";
import SettingSidebar from "../../SettingSidebar.vue";
import AdminPageHeader from "../../../../../common/layouts/AdminPageHeader.vue";
import LanguageSettings from "../../../common/settings/translations/langs/index.vue";
import ImportTranslation from "../../../../../common/core/ui/Import.vue";

export default {
    components: {
        PlusOutlined,
        SettingSidebar,
        AdminPageHeader,
        LanguageSettings,
        ImportTranslation,
    },
    setup() {
        const { permsArray, appType } = common();
        const langSettingRef = ref(null);
        const sampleFileUrl = window.config.translatioins_sample_file;

        const addItem = () => {
            langSettingRef.value.addItem();
        };

        return {
            permsArray,
            appType,
            langSettingRef,
            addItem,

            sampleFileUrl,
        };
    },
};
</script>

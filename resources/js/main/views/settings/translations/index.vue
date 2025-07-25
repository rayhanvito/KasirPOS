<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.translations`)" class="p-0" />
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
                    {{ $t("menu.translations") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <TranslationsSettings ref="transSettingRef">
                <template #actionButtons>
                    <a-space>
                        <LangAddButton
                            v-if="
                                permsArray.includes('translations_create') ||
                                permsArray.includes('admin')
                            "
                            @onAddSuccess="langAdded"
                            btnType="primary"
                            :tooltip="false"
                        >
                            {{ $t("langs.add") }}
                        </LangAddButton>
                        <ImportTranslation
                            v-if="appType == 'non-saas'"
                            :pageTitle="$t('translations.import_translations')"
                            :sampleFileUrl="sampleFileUrl"
                            importUrl="translations/import"
                            @onUploadSuccess="langAdded"
                        />
                        <a-button
                            type="primary"
                            @click="
                                $router.push({
                                    name: 'admin.settings.langs.index',
                                })
                            "
                        >
                            <EyeOutlined />
                            {{ $t("langs.view_all_langs") }}
                        </a-button>
                    </a-space>
                </template>
            </TranslationsSettings>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import { EyeOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import TranslationsSettings from "../../common/settings/translations/index.vue";
import LangAddButton from "../../common/settings/translations/langs/AddButton.vue";
import ImportTranslation from "../../../../common/core/ui/Import.vue";

export default {
    components: {
        EyeOutlined,
        SettingSidebar,
        AdminPageHeader,
        TranslationsSettings,
        LangAddButton,
        ImportTranslation,
    },
    setup() {
        const { permsArray, appType } = common();
        const transSettingRef = ref(null);
        const sampleFileUrl = window.config.translatioins_sample_file;

        const langAdded = () => {
            transSettingRef.value.getData();
        };

        return {
            langAdded,
            appType,
            permsArray,
            transSettingRef,

            sampleFileUrl,
        };
    },
};
</script>

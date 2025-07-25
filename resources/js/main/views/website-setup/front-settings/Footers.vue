<template>
    <a-card
        :title="$t('front_setting.footer')"
        class="page-content-container mt-20 mb-20"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.footer_company_description')"
                        name="footer_company_description"
                        :help="
                            rules.footer_company_description
                                ? rules.footer_company_description.message
                                : null
                        "
                        :validateStatus="
                            rules.footer_company_description ? 'error' : null
                        "
                    >
                        <a-input
                            v-model:value="
                                addEditForm.formData.footer_company_description
                            "
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('front_setting.copyright_text'),
                                ])
                            "
                        />
                    </a-form-item>

                    <a-form-item
                        :label="$t('front_setting.copyright_text')"
                        name="footer_copyright_text"
                        :help="
                            rules.footer_copyright_text
                                ? rules.footer_copyright_text.message
                                : null
                        "
                        :validateStatus="rules.footer_copyright_text ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.footer_copyright_text"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('front_setting.copyright_text'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-divider />

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.footer_contact_widget") }}
                    </a-typography-title>
                    <DyanmicForm
                        :data="formData.contact_info_widget"
                        :addText="$t('front_setting.addContactLink')"
                        @onEntry="updateContactInfoWidget"
                    />
                </a-col>
            </a-row>
            <a-divider />

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.footer_page_widget") }}
                    </a-typography-title>
                    <DyanmicForm
                        :data="formData.pages_widget"
                        :addText="$t('front_setting.addPageDetails')"
                        @onEntry="updatePagesWidget"
                    />
                </a-col>
            </a-row>
            <a-divider />
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.footer_links_widget") }}
                    </a-typography-title>
                    <DyanmicForm
                        :data="formData.links_widget"
                        :addText="$t('front_setting.addLink')"
                        @onEntry="updateLinkInfoWidget"
                    />
                </a-col>
            </a-row>
            <a-divider />

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
import { defineComponent, reactive, ref, onMounted, watch } from "vue";
import {
    SaveOutlined,
    SearchOutlined,
    DeleteOutlined,
    PlusOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import DyanmicForm from "./DyanmicForm.vue";

export default defineComponent({
    props: ["formData", "data", "rules"],
    emits: ["onSubmit"],
    components: {
        SaveOutlined,
        SearchOutlined,
        DeleteOutlined,
        PlusOutlined,
        MinusCircleOutlined,
        DyanmicForm,
    },
    setup(props, { emit }) {
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });

        onMounted(() => {
            addEditForm.formData = props.formData;
        });

        const updateContactInfoWidget = (resultArray) => {
            addEditForm.formData.contact_info_widget = resultArray;
            onSubmit();
        };

        const updatePagesWidget = (resultArray) => {
            addEditForm.formData.pages_widget = resultArray;
            onSubmit();
        };

        const updateLinkInfoWidget = (resultArray) => {
            addEditForm.formData.links_widget = resultArray;
            onSubmit();
        };

        const onSubmit = () => {
            emit("onSubmit", addEditForm.formData);
        };

        return {
            addEditForm,
            onSubmit,
            updateContactInfoWidget,
            updatePagesWidget,
            updateLinkInfoWidget,
        };
    },
});
</script>

<style></style>

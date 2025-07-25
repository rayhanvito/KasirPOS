<template>
    <a-card
        :title="$t('front_setting.social_links')"
        class="page-content-container mt-20 mb-20"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.facebook')"
                        name="name"
                        :help="rules.facebook_url ? rules.facebook_url.message : null"
                        :validateStatus="rules.facebook_url ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.facebook_url"
                            :placeholder="
                                $t('front_setting.placeholder_social_text', [
                                    $t('front_setting.facebook'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.twitter')"
                        name="name"
                        :help="rules.twitter_url ? rules.twitter_url.message : null"
                        :validateStatus="rules.twitter_url ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.twitter_url"
                            :placeholder="
                                $t('front_setting.placeholder_social_text', [
                                    $t('front_setting.twitter'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.instagram')"
                        name="name"
                        :help="rules.instagram_url ? rules.instagram_url.message : null"
                        :validateStatus="rules.instagram_url ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.instagram_url"
                            :placeholder="
                                $t('front_setting.placeholder_social_text', [
                                    $t('front_setting.instagram'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.linkedin')"
                        name="name"
                        :help="rules.linkedin_url ? rules.linkedin_url.message : null"
                        :validateStatus="rules.linkedin_url ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.linkedin_url"
                            :placeholder="
                                $t('front_setting.placeholder_social_text', [
                                    $t('front_setting.linkedin'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('front_setting.youtube')"
                        name="name"
                        :help="rules.youtube_url ? rules.youtube_url.message : null"
                        :validateStatus="rules.youtube_url ? 'error' : null"
                    >
                        <a-input
                            v-model:value="addEditForm.formData.youtube_url"
                            :placeholder="
                                $t('front_setting.placeholder_social_text', [
                                    $t('front_setting.youtube'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

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
import { defineComponent, reactive, onMounted } from "vue";
import { SaveOutlined, SearchOutlined, DeleteOutlined } from "@ant-design/icons-vue";

export default defineComponent({
    props: ["formData", "data", "rules"],
    emits: ["onSubmit"],
    components: {
        SaveOutlined,
        SearchOutlined,
        DeleteOutlined,
    },
    setup(props, { emit }) {
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });

        onMounted(() => {
            addEditForm.formData = props.formData;
        });

        const onSubmit = () => {
            emit("onSubmit", addEditForm.formData);
        };

        return {
            addEditForm,
            onSubmit,
        };
    },
});
</script>

<style></style>

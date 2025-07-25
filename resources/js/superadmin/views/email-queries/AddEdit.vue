<template>
    <a-modal
        :open="visible"
        :title="$t('email_queries.send_mail')"
        :closable="false"
        :centered="true"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_queries.email')"
                        name="email"
                        :help="rules.email ? rules.email.message : null"
                        :validateStatus="rules.email ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="data.email"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_queries.email'),
                                ])
                            "
                            disabled
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_queries.subject')"
                        name="subject"
                        :help="rules.subject ? rules.subject.message : null"
                        :validateStatus="rules.subject ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.subject"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_queries.subject'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('email_queries.body')"
                        name="body"
                        :help="rules.body ? rules.body.message : null"
                        :validateStatus="rules.body ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.body"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('email_queries.body'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SendOutlined />
                </template>
                {{ $t("common.send") }}
            </a-button>
            <a-button key="back" @click="onCloseCancel">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SendOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SendOutlined,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const formData = ref({
            xid: undefined,
            email: "",
            subject: "",
            body: "",
        });
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const onSubmit = () => {
            props.visible = false;
            addEditRequestAdmin({
                url: "/superadmin/email-queries/send-email",
                data: formData.value,
                successMessage: t("email_queries.eamil_sent_successfully"),
                success: (res) => {
                    emit("sentSuccess");
                },
            });
        };

        const onCloseCancel = () => {
            emit("onCancel");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.visible) {
                    formData.value = {
                        xid: props.data.xid,
                        email: props.data.email,
                        subject: "",
                        body: "",
                    };

                    rules.value = {};
                }
            }
        );

        return {
            loading,
            rules,
            onSubmit,
            onCloseCancel,
            formData,
        };
    },
});
</script>

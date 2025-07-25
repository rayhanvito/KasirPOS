<template>
    <a-modal
        v-model:open="visible"
        width="700px"
        :title="
            addEditId == null ? $t('front.add_new_address') : $t('front.edit_address')
        "
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('user.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.name')])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.email')"
                        name="email"
                        :help="rules.email ? rules.email.message : null"
                        :validateStatus="rules.email ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.email"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.email')])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.phone')"
                        name="phone"
                        :help="rules.phone ? rules.phone.message : null"
                        :validateStatus="rules.phone ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.phone"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.phone')])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('user.billing_address')"
                        name="address"
                        :help="rules.address ? rules.address.message : null"
                        :validateStatus="rules.address ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="address.address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.billing_address'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('user.shipping_address')"
                        name="shipping_address"
                        :help="
                            rules.shipping_address ? rules.shipping_address.message : null
                        "
                        :validateStatus="rules.shipping_address ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="address.shipping_address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.shipping_address'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.city')"
                        name="city"
                        :help="rules.city ? rules.city.message : null"
                        :validateStatus="rules.city ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.city"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.city')])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.state')"
                        name="state"
                        :help="rules.state ? rules.state.message : null"
                        :validateStatus="rules.state ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.state"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.state')])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.country')"
                        name="country"
                        :help="rules.country ? rules.country.message : null"
                        :validateStatus="rules.country ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.country"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.country'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.zipcode')"
                        name="zipcode"
                        :help="rules.zipcode ? rules.zipcode.message : null"
                        :validateStatus="rules.zipcode ? 'error' : null"
                    >
                        <a-input
                            v-model:value="address.zipcode"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('user.zipcode'),
                                ])
                            "
                            type="number"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-button type="primary" @click="onSubmit" style="margin-right: 8px">
                <template #icon> <SaveOutlined /> </template>
                {{ $t("common.save") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import { notification, message } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import processRequestFront from "../../../../../common/plugins/processRequestFront";

export default defineComponent({
    props: ["formData", "addEditVisible", "addEditId"],
    emits: ["addEditSuccess"],
    components: {
        SaveOutlined,
    },
    setup(props, { emit }) {
        const visible = ref(false);
        const address = ref({});
        const rules = ref({});
        const addEditId = ref(null);
        const { t } = useI18n();

        onMounted(() => {
            visible.value = props.addEditVisible;
            address.value = props.formData;
            addEditId.value = props.addEditId;
        });

        const onSubmit = () => {
            var url = "front/self/address";
            if (props.addEditId != null) {
                url = `front/self/address/${props.addEditId}`;
            }

            processRequestFront({
                url,
                data: address.value,
                success: (res) => {
                    // Toastr Notificaiton
                    notification.success({
                        placement: "bottomRight",
                        message: t("common.success"),
                        description: t("front.address_saved"),
                    });

                    emit("addEditSuccess");
                    rules.value = {};
                    visible.value = false;
                },
                error: (errorRules) => {
                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                },
            });
        };

        const onClose = () => {
            visible.value = false;
        };

        watch(props, (newVal, oldVal) => {
            visible.value = newVal.addEditVisible;
            address.value = newVal.formData;
            addEditId.value = newVal.addEditId;
        });

        return {
            address,
            rules,
            visible,
            onSubmit,
            onClose,
        };
    },
});
</script>

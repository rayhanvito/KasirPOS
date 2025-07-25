<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('tax.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('tax.name')])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('tax.tax_type')"
                        name="tax_type"
                        :help="rules.tax_type ? rules.tax_type.message : null"
                        :validateStatus="rules.tax_type ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.tax_type"
                            :placeholder="
                                $t('common.select_default_text', [$t('tax.tax_type')])
                            "
                            @change="
                                (newValue) => {
                                    formData.rate =
                                        newValue == 'multiple'
                                            ? sumBy(formFields, 'rate')
                                            : undefined;
                                }
                            "
                        >
                            <a-select-option value="single">
                                {{ $t("tax.single") }}
                            </a-select-option>
                            <a-select-option value="multiple">
                                {{ $t("tax.multiple") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('tax.rate')"
                        name="rate"
                        :help="rules.rate ? rules.rate.message : null"
                        :validateStatus="rules.rate ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.rate"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('tax.rate')])
                            "
                            min="0"
                            style="width: 100%"
                            :disabled="formData.tax_type == 'multiple'"
                        >
                            <template #addonAfter>%</template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
            </a-row>

            <form-item-heading v-if="formData.tax_type == 'multiple'">
                {{ $t("tax.multiple_tax") }}
            </form-item-heading>
            <a-row
                v-if="formData.tax_type == 'multiple'"
                :gutter="16"
                v-for="(formField, index) in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="addEditType == 'add' ? 11 : 12"
                    :lg="addEditType == 'add' ? 11 : 12"
                >
                    <a-form-item
                        name="tax_name"
                        :help="rules.tax_name ? rules.tax_name.message : null"
                        :validateStatus="rules.tax_name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formField.tax_name"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('tax.name')])
                            "
                            min="0"
                            style="width: 100%"
                        >
                        </a-input>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="addEditType == 'add' ? 11 : 12"
                    :lg="addEditType == 'add' ? 11 : 12"
                >
                    <a-form-item name="rate" class="required">
                        <a-input-number
                            v-model:value="formField.rate"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('tax.rate')])
                            "
                            style="width: 100%"
                            @change="
                                () => {
                                    formData.rate = sumBy(formFields, 'rate');
                                }
                            "
                        >
                            <template #addonAfter>%</template>
                        </a-input-number>
                    </a-form-item>
                </a-col>

                <a-col v-if="addEditType == 'add'" :span="2" style="margin-bottom: 20px">
                    <MinusSquareOutlined
                        @click="
                            () => {
                                removeFormField(formField);
                                formData.rate = sumBy(formFields, 'rate');
                            }
                        "
                    />
                </a-col>
            </a-row>
            <p
                v-if="rules && rules.multiple_taxes"
                class="ant-form-item-explain-error"
                :style="{ marginTop: '-15px' }"
            >
                {{ rules.multiple_taxes.message }}
            </p>
            <a-row :gutter="16" v-if="formData.tax_type == 'multiple'">
                <a-col :xs="24" :sm="24" :md="4" :lg="4">
                    <a-form-item>
                        <a-button
                            type="primary"
                            @click="addFormField"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{ $t("tax.add") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { forEach, some, filter, sumBy } from "lodash-es";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        MinusSquareOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const removedMultipleTax = ref([]);
        const formFields = ref([
            {
                tax_name: "",
                rate: "",
                id: undefined,
            },
        ]);

        const onSubmit = () => {
            var filtersFormFields = filter(formFields.value, function (newFormField) {
                return (
                    newFormField.tax_name != "" &&
                    newFormField.rate &&
                    newFormField.rate > 0
                );
            });

            var newFormData = {
                ...props.formData,
                multiple_taxes: filtersFormFields,
                removed_fields: removedMultipleTax.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { tax_name: "" }) ||
                    some(formFields.value, { tax_name: null }) ||
                    some(formFields.value, { rate: "" }) ||
                    some(formFields.value, { rate: null })
                );
            }
        });

        const addFormField = () => {
            formFields.value.push({
                tax_name: "",
                rate: "",
                id: "",
            });
        };

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                removedMultipleTax.value.push(item.id);
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.visible) {
                    formFields.value = [];
                    if (props.addEditType == "add") {
                        formFields.value.push({
                            tax_name: "",
                            rate: "",
                            id: "",
                        });
                    } else {
                        forEach(props.data.multiple_tax, (multipleTax) => {
                            formFields.value.push({
                                tax_name: multipleTax.name,
                                rate: multipleTax.rate,
                                id: multipleTax.xid,
                            });
                        });
                    }
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            removeFormField,
            formFields,
            addFormField,
            addFormButtonStatus,
            sumBy,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

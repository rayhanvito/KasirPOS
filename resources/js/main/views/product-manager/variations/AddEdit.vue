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
                        :label="$t('variation.variation_name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('variation.variation_name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <form-item-heading>
                {{ $t("variation.variation_values") }}
            </form-item-heading>
            <a-row
                :gutter="16"
                v-for="(formField, index) in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col :xs="24" :sm="24" :md="23" :lg="23">
                    <a-form-item :label="$t('variation.value')" name="name">
                        <a-input
                            v-model:value="formField.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('variation.value'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col v-if="addEditType == 'add'" :span="1" style="margin-top: 6px">
                    <MinusSquareOutlined @click="removeFormField(formField)" />
                </a-col>
            </a-row>
            <p v-if="rules.value" style="color: red">
                {{ rules.value.message }}
            </p>
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item>
                    <a-button
                        type="dashed"
                        block
                        @click="addFormField"
                        :disabled="addFormButtonStatus"
                    >
                        <PlusOutlined />

                        {{ $t("variation.add_new_value") }}
                    </a-button>
                </a-form-item>
            </a-col>
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
import { defineComponent, ref, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import common from "../../../../common/composable/common";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { some, forEach, filter } from "lodash-es";

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
        Upload,
        MinusSquareOutlined,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { slugify } = common();
        const formFields = ref([
            {
                name: "",
                id: "",
            },
        ]);
        const removedVariations = ref([]);

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                value: formFieldFilter(),
                removed_variations: removedVariations.value,
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
        const addFormField = () => {
            formFields.value.push({
                name: "",
                id: "",
            });
        };

        const formFieldFilter = () => {
            var newFormField = [];

            forEach(formFields.value, (formField) => {
                if (formField.name != "") {
                    newFormField.push(formField);
                }
            });

            return newFormField;
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { name: "" }) ||
                    some(formFields.value, { name: null })
                );
            }
        });

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                removedVariations.value.push(item.id);
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
                    if (props.addEditType == "edit") {
                        forEach(props.data.sub_variations, (subVariation) => {
                            formFields.value.push({
                                name: subVariation.name,
                                id: subVariation.xid,
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
            slugify,
            formFields,
            removeFormField,
            addFormField,
            addFormButtonStatus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

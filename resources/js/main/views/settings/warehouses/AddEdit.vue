<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="basic_details">
                    <template #tab>
                        <span>
                            <FileTextOutlined />
                            {{ $t("warehouse.basic_details") }}
                        </span>
                    </template>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('warehouse.name')"
                                        name="name"
                                        :help="rules.name ? rules.name.message : null"
                                        :validateStatus="rules.name ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.name"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('warehouse.name'),
                                                ])
                                            "
                                            v-on:keyup="
                                                formData.slug = slugify(
                                                    $event.target.value
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('warehouse.slug')"
                                        name="slug"
                                        :help="rules.slug ? rules.slug.message : null"
                                        :validateStatus="rules.slug ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.slug"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('warehouse.slug'),
                                                ])
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="16" :lg="16">
                                    <a-form-item
                                        :label="$t('warehouse.email')"
                                        name="email"
                                        :help="rules.email ? rules.email.message : null"
                                        :validateStatus="rules.email ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.email"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('warehouse.email'),
                                                ])
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                    <a-form-item
                                        :label="$t('warehouse.show_email_on_invoice')"
                                        name="show_email_on_invoice"
                                        :help="
                                            rules.show_email_on_invoice
                                                ? rules.show_email_on_invoice.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.show_email_on_invoice ? 'error' : null
                                        "
                                    >
                                        <a-switch
                                            v-model:checked="
                                                formData.show_email_on_invoice
                                            "
                                            :checkedValue="1"
                                            :unCheckedValue="0"
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="16" :lg="16">
                                    <a-form-item
                                        :label="$t('warehouse.phone')"
                                        name="phone"
                                        :help="rules.phone ? rules.phone.message : null"
                                        :validateStatus="rules.phone ? 'error' : null"
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.phone"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('warehouse.phone'),
                                                ])
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                                    <a-form-item
                                        :label="$t('warehouse.show_phone_on_invoice')"
                                        name="show_phone_on_invoice"
                                        :help="
                                            rules.show_phone_on_invoice
                                                ? rules.show_phone_on_invoice.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.show_phone_on_invoice ? 'error' : null
                                        "
                                    >
                                        <a-switch
                                            v-model:checked="
                                                formData.show_phone_on_invoice
                                            "
                                            :checkedValue="1"
                                            :unCheckedValue="0"
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="6" :lg="6">
                            <a-row :gutter="16">
                                <a-col :span="24">
                                    <a-form-item
                                        :label="$t('warehouse.logo')"
                                        name="logo"
                                        :help="rules.logo ? rules.logo.message : null"
                                        :validateStatus="rules.logo ? 'error' : null"
                                    >
                                        <Upload
                                            :formData="formData"
                                            folder="warehouses"
                                            imageField="logo"
                                            @onFileUploaded="
                                                (file) => {
                                                    formData.logo = file.file;
                                                    formData.logo_url = file.file_url;
                                                }
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="6" :lg="6">
                            <a-row :gutter="16">
                                <a-col :span="24">
                                    <a-form-item
                                        :label="$t('warehouse.dark_logo')"
                                        name="dark_logo"
                                        :help="
                                            rules.dark_logo
                                                ? rules.dark_logo.message
                                                : null
                                        "
                                        :validateStatus="rules.dark_logo ? 'error' : null"
                                    >
                                        <Upload
                                            :formData="formData"
                                            folder="warehouses"
                                            imageField="dark_logo"
                                            @onFileUploaded="
                                                (file) => {
                                                    formData.dark_logo = file.file;
                                                    formData.dark_logo_url =
                                                        file.file_url;
                                                }
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.address')"
                                name="address"
                                :help="rules.address ? rules.address.message : null"
                                :validateStatus="rules.address ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="formData.address"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('warehouse.address'),
                                        ])
                                    "
                                    :auto-size="{ minRows: 2, maxRows: 3 }"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item>
                                <a-typography-paragraph type="warning" strong>
                                    <blockquote>
                                        {{
                                            $t(
                                                "warehouse.details_will_be_shown_on_invoice"
                                            )
                                        }}
                                    </blockquote>
                                </a-typography-paragraph>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.bank_details')"
                                name="bank_details"
                                :help="
                                    rules.bank_details ? rules.bank_details.message : null
                                "
                                :validateStatus="rules.bank_details ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="formData.bank_details"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('warehouse.bank_details'),
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
                                :label="$t('warehouse.terms_condition')"
                                name="terms_condition"
                                :help="
                                    rules.terms_condition
                                        ? rules.terms_condition.message
                                        : null
                                "
                                :validateStatus="rules.terms_condition ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="formData.terms_condition"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('warehouse.terms_condition'),
                                        ])
                                    "
                                    :auto-size="{ minRows: 2, maxRows: 3 }"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('warehouse.signature')"
                                name="signature"
                                :help="rules.signature ? rules.signature.message : null"
                                :validateStatus="rules.signature ? 'error' : null"
                            >
                                <Upload
                                    :formData="formData"
                                    folder="warehouses"
                                    imageField="signature"
                                    @onFileUploaded="
                                        (file) => {
                                            formData.signature = file.file;
                                            formData.signature_url = file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="visibility">
                    <template #tab>
                        <span>
                            <EyeOutlined />
                            {{ $t("warehouse.visibility") }}
                        </span>
                    </template>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.customers_visibility')"
                                name="customers_visibility"
                                :help="
                                    rules.customers_visibility
                                        ? rules.customers_visibility.message
                                        : null
                                "
                                :validateStatus="
                                    rules.customers_visibility ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.customers_visibility"
                                >
                                    <a-radio :style="radioStyle" value="all">
                                        {{ $t("warehouse.view_all_customers") }}
                                    </a-radio>
                                    <a-radio :style="radioStyle" value="warehouse">
                                        {{ $t("warehouse.view_warehouse_customers") }}
                                    </a-radio>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.suppliers_visibility')"
                                name="suppliers_visibility"
                                :help="
                                    rules.suppliers_visibility
                                        ? rules.suppliers_visibility.message
                                        : null
                                "
                                :validateStatus="
                                    rules.suppliers_visibility ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.suppliers_visibility"
                                >
                                    <a-radio :style="radioStyle" value="all">
                                        {{ $t("warehouse.view_all_suppliers") }}
                                    </a-radio>
                                    <a-radio :style="radioStyle" value="warehouse">
                                        {{ $t("warehouse.view_warehouse_suppliers") }}
                                    </a-radio>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.products_visibility')"
                                name="products_visibility"
                                :help="
                                    rules.products_visibility
                                        ? rules.products_visibility.message
                                        : null
                                "
                                :validateStatus="
                                    rules.products_visibility ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.products_visibility"
                                >
                                    <a-radio :style="radioStyle" value="all">
                                        {{ $t("warehouse.view_all_products") }}
                                    </a-radio>
                                    <a-radio :style="radioStyle" value="warehouse">
                                        {{ $t("warehouse.view_warehouse_products") }}
                                    </a-radio>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="pos_settings">
                    <template #tab>
                        <span>
                            <SettingOutlined />
                            {{ $t("menu.pos_settings") }}
                        </span>
                    </template>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('warehouse.default_pos_order_status')"
                                name="default_pos_order_status"
                                :help="
                                    rules.default_pos_order_status
                                        ? rules.default_pos_order_status.message
                                        : null
                                "
                                :validateStatus="
                                    rules.default_pos_order_status ? 'error' : null
                                "
                            >
                                <a-select
                                    v-model:value="formData.default_pos_order_status"
                                    :placeholder="
                                        $t('warehouse.default_pos_order_status')
                                    "
                                    style="width: 100%"
                                >
                                    <a-select-option
                                        v-for="orderStatus in salesOrderStatus"
                                        :key="orderStatus.key"
                                        :value="orderStatus.key"
                                    >
                                        {{ orderStatus.value }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('warehouse.show_mrp_on_invoice')"
                                name="show_mrp_on_invoice"
                                :help="
                                    rules.show_mrp_on_invoice
                                        ? rules.show_mrp_on_invoice.message
                                        : null
                                "
                                :validateStatus="
                                    rules.show_mrp_on_invoice ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.show_mrp_on_invoice"
                                    size="small"
                                    buttonStyle="solid"
                                >
                                    <a-radio-button :value="1">
                                        {{ $t("common.yes") }}
                                    </a-radio-button>
                                    <a-radio-button :value="0">
                                        {{ $t("common.no") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('warehouse.show_discount_tax_on_invoice')"
                                name="show_discount_tax_on_invoice"
                                :help="
                                    rules.show_discount_tax_on_invoice
                                        ? rules.show_discount_tax_on_invoice.message
                                        : null
                                "
                                :validateStatus="
                                    rules.show_discount_tax_on_invoice ? 'error' : null
                                "
                            >
                                <a-radio-group
                                    v-model:value="formData.show_discount_tax_on_invoice"
                                    size="small"
                                    buttonStyle="solid"
                                >
                                    <a-radio-button :value="1">
                                        {{ $t("common.yes") }}
                                    </a-radio-button>
                                    <a-radio-button :value="0">
                                        {{ $t("common.no") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
            </a-tabs>
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, reactive } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    FileTextOutlined,
    EyeOutlined,
    SettingOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import common from "../../../../common/composable/common";
import { useStore } from "vuex";

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
    emits: ["addEditSuccess"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        FileTextOutlined,
        EyeOutlined,
        SettingOutlined,

        Upload,
    },
    setup(props, { emit }) {
        const store = useStore();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { slugify, salesOrderStatus, selectedWarehouse } = common();
        const activeKey = ref("basic_details");
        const radioStyle = reactive({
            display: "flex",
            height: "30px",
            lineHeight: "30px",
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    store.dispatch("auth/updateAllWarehouses");

                    if (
                        selectedWarehouse.value &&
                        selectedWarehouse.value.xid &&
                        selectedWarehouse.value.xid == res.xid
                    ) {
                        axiosAdmin
                            .post("change-warehouse", {
                                warehouse_id: res.xid,
                            })
                            .then((response) => {
                                store.commit(
                                    "auth/updateWarehouse",
                                    response.data.warehouse
                                );
                            });
                    }

                    emit("addEditSuccess", res.xid);

                    activeKey.value = "basic_details";
                    rules.value = {};
                },
            });
        };

        const onClose = () => {
            activeKey.value = "basic_details";
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            slugify,
            activeKey,

            salesOrderStatus,
            radioStyle,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>

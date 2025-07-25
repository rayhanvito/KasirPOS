<template>
    <a-button type="primary" @click="openDrawer">
        <template #icon> <SettingOutlined /> </template>
        {{ $t("company.shortcut_menu_setting") }}
    </a-button>

    <a-drawer
        :title="$t('company.shortcut_menu_setting')"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company.shortcut_menu_Placement')"
                        name="shortcut_menus"
                        :help="rules.shortcut_menus ? rules.shortcut_menus.message : null"
                        :validateStatus="rules.shortcut_menus ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="createMenuPosition"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('company.shortcut_menu_Placement'),
                                ])
                            "
                            :allowClear="true"
                            optionFilterProp="label"
                            show-search
                        >
                            <a-select-option key="top_bottom" value="top_bottom">
                                {{ $t("company.top_and_bottom") }}
                            </a-select-option>
                            <a-select-option key="top" value="top">
                                {{ $t("company.top_header") }}
                            </a-select-option>
                            <a-select-option key="bottom" value="bottom">
                                {{ $t("company.bottom_corner") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company.shortcut_menu_setting')"
                        name="shortcut_menus"
                        :help="rules.createMenus ? rules.shortcut_menus.message : null"
                        :validateStatus="rules.shortcut_menus ? 'error' : null"
                    >
                        <a-checkbox-group v-model:value="createMenus" style="width: 100%">
                            <a-row :gutter="[16, 10]">
                                <a-col :span="24">
                                    <a-checkbox value="staff_member">
                                        {{ $t("topbar_add_button.add_staff_member") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="customer">
                                        {{ $t("topbar_add_button.add_customer") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="supplier">
                                        {{ $t("topbar_add_button.add_supplier") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="brand">
                                        {{ $t("topbar_add_button.add_brand") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="category">
                                        {{ $t("topbar_add_button.add_category") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="product">
                                        {{ $t("topbar_add_button.add_product") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="sales">
                                        {{ $t("topbar_add_button.add_sales") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="purchase">
                                        {{ $t("topbar_add_button.add_purchase") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="expense_category">
                                        {{
                                            $t("topbar_add_button.add_expense_cateogory")
                                        }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="expense">
                                        {{ $t("topbar_add_button.add_expense") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="currency">
                                        {{ $t("topbar_add_button.add_currency") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="warehouse">
                                        {{ $t("topbar_add_button.add_warehouse") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="unit">
                                        {{ $t("topbar_add_button.add_unit") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="language">
                                        {{ $t("topbar_add_button.add_language") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="role">
                                        {{ $t("topbar_add_button.add_role") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="tax">
                                        {{ $t("topbar_add_button.add_tax") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="payment_mode">
                                        {{ $t("topbar_add_button.add_payment_mode") }}
                                    </a-checkbox>
                                </a-col>
                            </a-row>
                        </a-checkbox-group>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{ $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SettingOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";

export default defineComponent({
    emits: ["success"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SettingOutlined,
    },
    setup(props, { emit }) {
        const { appSetting, addMenus } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const createMenuPosition = ref(appSetting.value.shortcut_menus);
        const createMenus = ref(addMenus.value);
        const visible = ref(false);
        const store = useStore();

        const onSubmit = () => {
            const newFormData = {
                position: createMenuPosition.value,
                shortcut_menus_settings: createMenus.value,
            };

            addEditRequestAdmin({
                url: "companies/update-create-menu",
                data: newFormData,
                successMessage: t("company.menu_setting_updated"),
                success: (res) => {
                    emit("success", createMenuPosition.value);
                    store.dispatch("auth/updateApp");
                    visible.value = false;
                },
            });
        };

        const openDrawer = () => {
            rules.value = {};
            visible.value = true;
        };

        const onClose = () => {
            rules.value = {};
            visible.value = false;
        };

        watch(appSetting, (newVal, oldVal) => {
            createMenuPosition.value = newVal.shortcut_menus;
        });

        return {
            visible,
            loading,
            rules,
            onClose,
            openDrawer,
            onSubmit,
            createMenuPosition,
            createMenus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "35%",
        };
    },
});
</script>

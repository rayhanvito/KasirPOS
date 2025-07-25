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
            <a-row>
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('user.profile_image')"
                        name="profile_image"
                        :help="rules.profile_image ? rules.profile_image.message : null"
                        :validateStatus="rules.profile_image ? 'error' : null"
                    >
                        <Upload
                            :formData="formData"
                            folder="user"
                            imageField="profile_image"
                            @onFileUploaded="
                                (file) => {
                                    formData.profile_image = file.file;
                                    formData.profile_image_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="18" :lg="18">
                    <a-row :gutter="16" v-if="warehouseVisible">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                name="warehouse_id"
                                :help="
                                    rules.warehouse_id ? rules.warehouse_id.message : null
                                "
                                :validateStatus="rules.warehouse_id ? 'error' : null"
                                class="required"
                            >
                                <template #label>
                                    {{ $t("warehouse.warehouse") }}
                                </template>
                                <span
                                    v-if="permsArray.includes('admin')"
                                    style="display: flex"
                                >
                                    <a-select
                                        v-if="formData.user_type == 'staff_members'"
                                        v-model:value="formData.warehouses"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('warehouse.warehouse'),
                                            ])
                                        "
                                        mode="multiple"
                                        @change="warehousesChanged"
                                    >
                                        <a-select-option
                                            v-for="warehouse in warehouses"
                                            :key="warehouse.xid"
                                            :value="warehouse.xid"
                                        >
                                            {{ warehouse.name }}
                                        </a-select-option>
                                    </a-select>
                                    <a-select
                                        v-else
                                        v-model:value="formData.warehouse_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('warehouse.warehouse'),
                                            ])
                                        "
                                    >
                                        <a-select-option
                                            v-for="warehouse in warehouses"
                                            :key="warehouse.xid"
                                            :value="warehouse.xid"
                                        >
                                            {{ warehouse.name }}
                                        </a-select-option>
                                    </a-select>
                                    <WarehouseAddButton @onAddSuccess="warehouseAdded" />
                                </span>
                                <span v-else>
                                    <a-select
                                        :value="selectedWarehouse.xid"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('warehouse.warehouse'),
                                            ])
                                        "
                                        disabled
                                    >
                                        <a-select-option :value="selectedWarehouse.xid">
                                            {{ selectedWarehouse.name }}
                                        </a-select-option>
                                    </a-select>
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row
                        :gutter="16"
                        v-if="
                            formData.user_type == 'staff_members' &&
                            permsArray.includes('admin')
                        "
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('user.role')"
                                name="role_id"
                                :help="rules.role_id ? rules.role_id.message : null"
                                :validateStatus="rules.role_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.role_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.role'),
                                            ])
                                        "
                                        :allowClear="true"
                                        @change="roleChanged"
                                    >
                                        <a-select-option
                                            v-for="role in roles"
                                            :key="role.xid"
                                            :value="role.xid"
                                            :title="role.name"
                                        >
                                            {{ role.display_name }}
                                        </a-select-option>
                                    </a-select>
                                    <RoleAddButton @onAddSuccess="roleAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.name')"
                                name="name"
                                :help="rules.name ? rules.name.message : null"
                                :validateStatus="rules.name ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.name"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.name'),
                                        ])
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
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.phone"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.phone'),
                                        ])
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
                                :class="{
                                    required: formData.user_type == 'staff_members',
                                }"
                            >
                                <a-input
                                    v-model:value="formData.email"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.email'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.status')"
                                name="status"
                                :help="rules.status ? rules.status.message : null"
                                :validateStatus="rules.status ? 'error' : null"
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.status"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.status'),
                                        ])
                                    "
                                >
                                    <a-select-option value="enabled">
                                        {{ $t("common.enabled") }}
                                    </a-select-option>
                                    <a-select-option value="disabled">
                                        {{ $t("common.disabled") }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row
                        v-if="
                            formData.user_type == 'staff_members' ||
                            formData.user_type == 'customers'
                        "
                    >
                        <a-col :span="24">
                            <a-form-item
                                :label="$t('user.password')"
                                name="password"
                                :help="rules.password ? rules.password.message : null"
                                :validateStatus="rules.password ? 'error' : null"
                                :class="
                                    formData.user_type == 'staff_members'
                                        ? 'required'
                                        : ''
                                "
                            >
                                <a-input-password
                                    v-model:value="formData.password"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.password'),
                                        ])
                                    "
                                />
                                <small
                                    v-if="addEditType == 'edit'"
                                    class="small-text-message"
                                >
                                    {{ $t("user.password_blank") }}
                                </small>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16" v-if="formData.user_type != 'staff_members'">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('user.tax_number')"
                                name="tax_number"
                                :help="rules.tax_number ? rules.tax_number.message : null"
                                :validateStatus="rules.tax_number ? 'error' : null"
                            >
                                <a-input
                                    v-model:value="formData.tax_number"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.tax_number'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16" v-if="formData.user_type != 'staff_members'">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('user.opening_balance')"
                                name="opening_balance"
                                :help="
                                    rules.opening_balance
                                        ? rules.opening_balance.message
                                        : null
                                "
                                :validateStatus="rules.opening_balance ? 'error' : null"
                            >
                                <a-input
                                    v-model:value="formData.opening_balance"
                                    placeholder="0"
                                >
                                    <template #prefix>
                                        {{ appSetting.currency.symbol }}
                                    </template>
                                    <template #addonAfter>
                                        <a-select
                                            v-model:value="formData.opening_balance_type"
                                            style="width: 100px"
                                        >
                                            <a-select-option value="receive">
                                                {{ $t("user.receive") }}
                                            </a-select-option>
                                            <a-select-option value="pay">
                                                {{ $t("user.pay") }}
                                            </a-select-option>
                                        </a-select>
                                    </template>
                                </a-input>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>

            <a-row :gutter="16" v-if="formData.user_type != 'staff_members'">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.credit_period')"
                        name="credit_period"
                        :help="rules.credit_period ? rules.credit_period.message : null"
                        :validateStatus="rules.credit_period ? 'error' : null"
                    >
                        <a-input-number
                            v-model:value="formData.credit_period"
                            placeholder="0"
                            :addon-after="$t('user.days')"
                            type="number"
                            :precision="0"
                            :style="{ width: '100%' }"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('user.credit_limit')"
                        name="credit_limit"
                        :help="rules.credit_limit ? rules.credit_limit.message : null"
                        :validateStatus="rules.credit_limit ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.credit_limit"
                            placeholder="0"
                            :addon-before="appSetting.currency.symbol"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="
                            formData.user_type == 'staff_members'
                                ? $t('user.address')
                                : $t('user.billing_address')
                        "
                        name="address"
                        :help="rules.address ? rules.address.message : null"
                        :validateStatus="rules.address ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    formData.user_type == 'staff_members'
                                        ? $t('user.address')
                                        : $t('user.billing_address'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16" v-if="formData.user_type != 'staff_members'">
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
                            v-model:value="formData.shipping_address"
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
import { defineComponent, ref, onMounted, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { forEach } from "lodash-es";
import apiAdmin from "../../../common/composable/apiAdmin";
import Upload from "../../../common/core/ui/file/Upload.vue";
import WarehouseAddButton from "../settings/warehouses/AddButton.vue";
import RoleAddButton from "../settings/roles/AddButton.vue";
import common from "../../../common/composable/common";
import TooltipWarehouse from "./TooltipWarehouse.vue";

export default defineComponent({
    props: [
        "addEditData",
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
        RoleAddButton,
        WarehouseAddButton,
        TooltipWarehouse,
    },
    setup(props, { emit }) {
        const { permsArray, user, appSetting, selectedWarehouse } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const roles = ref([]);
        const warehouses = ref([]);
        const formData = ref({});
        const roleUrl = "roles?limit=10000";
        const warehouseUrl = "warehouses?limit=10000";
        const store = useStore();
        const warehouseVisible = ref(true);

        onMounted(() => {
            const rolesPromise = axiosAdmin.get(roleUrl);
            const warehousesPromise = axiosAdmin.get(warehouseUrl);

            Promise.all([rolesPromise, warehousesPromise]).then(
                ([rolesResponse, warehousesResponse]) => {
                    roles.value = rolesResponse.data;
                    warehouses.value = warehousesResponse.data;
                }
            );

            formData.value = { ...props.addEditData };
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: formData.value,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);

                    if (user.value.xid == res.xid) {
                        store.dispatch("auth/updateUser");
                    }
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const roleAdded = () => {
            axiosAdmin.get(roleUrl).then((response) => {
                roles.value = response.data;
            });
        };

        const warehouseAdded = () => {
            axiosAdmin.get(warehouseUrl).then((response) => {
                warehouses.value = response.data;
            });
        };

        const warehousesChanged = (warehouseValues, options) => {
            formData.value = {
                ...formData.value,
                warehouse_id: warehouseValues.length > 0 ? warehouseValues[0] : undefined,
            };
        };

        const roleChanged = (value, option) => {
            if (option && option.title && option.title == "admin") {
                warehouseVisible.value = false;
            } else {
                warehouseVisible.value = true;
            }
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    warehouseVisible.value = true;
                    if (props.addEditType == "edit") {
                        warehouseVisible.value = true;

                        if (
                            props.data.user_type == "staff_members" &&
                            props.data.role &&
                            props.data.role.name == "admin"
                        ) {
                            warehouseVisible.value = false;
                        }

                        if (
                            props.data.user_type == "customers" &&
                            props.data.is_walkin_customer
                        ) {
                            warehouseVisible.value = false;
                        }
                    }

                    if (props.addEditType == "add") {
                        if (props.addEditData.user_type == "staff_members") {
                            formData.value = {
                                ...props.addEditData,
                                warehouse_id: selectedWarehouse.value.xid,
                                warehouses: [selectedWarehouse.value.xid],
                            };
                        } else {
                            formData.value = {
                                ...props.addEditData,
                                warehouse_id: selectedWarehouse.value.xid,
                                warehouses: [],
                            };
                        }
                    } else {
                        var addEditUserWarehouseId =
                            props.data.warehouse && props.data.warehouse.xid
                                ? props.data.warehouse.xid
                                : undefined;

                        var addEditUserWarehouses = [];
                        if (props.addEditData.user_type == "staff_members") {
                            var userWarehouses = props.data.user_warehouses;
                            forEach(userWarehouses, (userWarehouseValue) => {
                                addEditUserWarehouses.push(
                                    userWarehouseValue.x_warehouse_id
                                );
                            });
                        }

                        formData.value = {
                            ...props.addEditData,
                            role_id:
                                props.data.role && props.data.role.xid
                                    ? props.data.role.xid
                                    : undefined,
                            warehouse_id: addEditUserWarehouseId,
                            warehouses: addEditUserWarehouses,
                            opening_balance:
                                props.data.details && props.data.details.opening_balance
                                    ? props.data.details.opening_balance
                                    : undefined,
                            opening_balance_type:
                                props.data.details &&
                                props.data.details.opening_balance_type
                                    ? props.data.details.opening_balance_type
                                    : undefined,
                            credit_period:
                                props.data.details && props.data.details.credit_period
                                    ? props.data.details.credit_period
                                    : undefined,
                            credit_limit:
                                props.data.details && props.data.details.credit_limit
                                    ? props.data.details.credit_limit
                                    : undefined,
                            _method: "PUT",
                        };
                    }
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            roles,
            warehouses,
            formData,

            roleAdded,
            warehouseAdded,
            permsArray,
            appSetting,
            selectedWarehouse,
            warehousesChanged,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",

            warehouseVisible,
            roleChanged,
        };
    },
});
</script>

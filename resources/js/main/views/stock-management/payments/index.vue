<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.payment_${paymentType}`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.${menuParent}`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.payment_${paymentType}`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes(
                                paymentType == 'in'
                                    ? 'payment_in_create'
                                    : 'payment_out_create'
                            ) || permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("payments.add") }}
                        </a-button>
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes(
                                paymentType == 'in'
                                    ? 'payment_in_delete'
                                    : 'payment_out_delete'
                            ) ||
                                permsArray.includes('admin'))
                        "
                        type="primary"
                        @click="showSelectedDeleteConfirm"
                        danger
                    >
                        <template #icon><DeleteOutlined /></template>
                        {{ $t("common.delete") }}
                    </a-button>
                </a-space>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="8">
                        <a-input-group compact>
                            <a-select
                                style="width: 25%"
                                v-model:value="table.searchColumn"
                                :placeholder="$t('common.select_default_text', [''])"
                            >
                                <a-select-option
                                    v-for="filterableColumn in filterableColumns"
                                    :key="filterableColumn.key"
                                >
                                    {{ filterableColumn.value }}
                                </a-select-option>
                            </a-select>
                            <a-input-search
                                style="width: 75%"
                                v-model:value="table.searchString"
                                show-search
                                @change="onTableSearch"
                                @search="onTableSearch"
                                :loading="table.filterLoading"
                            />
                        </a-input-group>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.user_id"
                            :placeholder="
                                $t('common.select_default_text', [$t(`user.user`)])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="user in users"
                                :key="user.xid"
                                :title="user.name"
                                :value="user.xid"
                            >
                                {{ user.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <AddEdit
            :addEditType="addEditType"
            :visible="addEditVisible"
            :url="addEditUrl"
            @addEditSuccess="addEditSuccess"
            @closed="onCloseAddEdit"
            :formData="formData"
            :data="viewData"
            :pageTitle="pageTitle"
            :successMessage="successMessage"
        />

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'date'">
                                {{ formatDate(record.date) }}
                            </template>
                            <template v-if="column.dataIndex === 'user_id'">
                                <UserInfo :user="record.user" />
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                {{ formatAmountCurrency(record.amount) }}
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes(
                                            paymentType == 'in'
                                                ? 'payment_in_edit'
                                                : 'payment_out_edit'
                                        ) || permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EditOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        permsArray.includes(
                                            paymentType == 'in'
                                                ? 'payment_in_delete'
                                                : 'payment_out_delete'
                                        ) || permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><DeleteOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted, reactive, ref, watch } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import fields from "./fields";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import UserInfo from "../../../../common/components/user/UserInfo.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        UserInfo,
    },
    setup() {
        const {
            addEditUrl,
            initData,
            columns,
            paymentType,
            menuParent,
            hashableColumns,
            filterableColumns,
        } = fields();
        const crudVariables = crud();
        const {
            permsArray,
            formatAmountCurrency,
            selectedWarehouse,
            formatDate,
        } = common();
        const filters = reactive({
            user_id: undefined,
        });
        const users = ref({});
        const route = useRoute();

        onMounted(() => {
            getInitialData();

            setUrlData();
        });

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: `payment-${paymentType.value}?fields=id,xid,date,amount,notes,payment_mode_id,payment_number,payment_type,payment_mode_id,x_payment_mode_id,paymentMode{id,xid,name},user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url,user_type}`,
                filterString: `payment_type eq "${paymentType.value}"`,
                filters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl.value;
            crudVariables.langKey.value = "payments";
            crudVariables.initData.value = { ...initData.value };
            crudVariables.formData.value = { ...initData.value };
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        const getInitialData = () => {
            const usersPromise = axiosAdmin.post("customer-suppliers");

            Promise.all([usersPromise]).then(([usersResponse]) => {
                users.value = usersResponse.data;
            });
        };

        watch(selectedWarehouse, (newVal, oldVal) => {
            setUrlData();
        });

        watch(
            () => route.meta.paymentType,
            (newVal, oldVal) => {
                // if (newVal == "in" || newVal == "out") {
                //     paymentType.value = route.meta.paymentType;
                //     menuParent.value = route.meta.menuParent;
                //     addEditUrl.value = `payment-${paymentType.value}`;
                //     initData.value = {
                //         ...initData.value,
                //         payment_type: paymentType.value,
                //     };
                //     filters.user_id = undefined;
                //     setUrlData();
                // }
            }
        );

        return {
            columns,
            ...crudVariables,
            permsArray,
            formatAmountCurrency,
            paymentType,
            menuParent,
            filters,
            users,
            setUrlData,
            filterableColumns,
            formatDate,
        };
    },
};
</script>

<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.email_queries`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.email_queries`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="10" :lg="8" :xl="6">
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
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <AddEdit
            :visible="visible"
            :data="detail"
            @onCancel="onCancel"
            @sentSuccess="onCancel"
        />

        <EmailNotSetup class="mb-20" />

        <a-row>
            <a-col :span="24">
                <a-tabs v-model:activeKey="filters.replied" @change="setUrlData">
                    <a-tab-pane key="0">
                        <template #tab>
                            <span>
                                <FileExcelOutlined />
                            </span>
                            {{ $t("email_queries.not_replied") }}
                        </template>
                    </a-tab-pane>
                    <a-tab-pane key="1">
                        <template #tab>
                            <span>
                                <FileDoneOutlined />
                            </span>
                            {{ $t("email_queries.replied") }}
                        </template>
                    </a-tab-pane>
                </a-tabs>
            </a-col>
        </a-row>

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
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
                            <template v-if="column.dataIndex == 'date_time'">
                                {{ formatDateTime(record.date_time) }}
                            </template>
                            <template v-if="column.dataIndex == 'replied'">
                                <CheckOutlined
                                    v-if="record.replied"
                                    :style="{ color: 'green' }"
                                />
                                <CloseOutlined v-else :style="{ color: 'red' }" />
                            </template>
                            <template v-if="column.dataIndex == 'action'">
                                <a-button
                                    type="primary"
                                    @click="viewSendMail(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><SendOutlined /></template>
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
import { ref, reactive, onMounted } from "vue";
import {
    PlusOutlined,
    SendOutlined,
    FileExcelOutlined,
    FileDoneOutlined,
    CheckOutlined,
    CloseOutlined,
} from "@ant-design/icons-vue";
import datatable from "../../../common/composable/datatable";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import common from "../../../common/composable/common";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import EmailNotSetup from "../settings/email/EmailNotSetup.vue";

export default {
    components: {
        PlusOutlined,
        SendOutlined,
        CheckOutlined,
        CloseOutlined,
        FileExcelOutlined,
        FileDoneOutlined,

        AddEdit,
        AdminPageHeader,
        EmailNotSetup,
    },
    setup() {
        const { formatDateTime, permsArray } = common();
        const { url, columns, filterableColumns } = fields();
        const { table, tableUrl, fetch, handleTableChange, onTableSearch } = datatable();
        const visible = ref(false);
        const detail = ref({});
        const filters = reactive({
            replied: "0",
        });

        onMounted(() => {
            table.filterableColumns = filterableColumns;

            setUrlData();
        });

        const setUrlData = () => {
            tableUrl.value = { url, filters };

            fetch({
                page: 1,
            });
        };

        const viewSendMail = (item) => {
            visible.value = true;
            detail.value = item;
        };

        const onCancel = () => {
            visible.value = false;
        };

        return {
            filters,
            table,
            permsArray,
            columns,
            filterableColumns,
            handleTableChange,
            onTableSearch,
            formatDateTime,

            viewSendMail,
            onCancel,
            visible,
            detail,

            setUrlData,
        };
    },
};
</script>

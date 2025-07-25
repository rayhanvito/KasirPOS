<template>
    <SuperAdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.payment_transactions`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.payment_transactions`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </SuperAdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="16" :lg="8" :xl="6">
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
                            <template v-if="column.dataIndex === 'company'">
                                {{ record.company.name }}
                            </template>
                            <template v-if="column.dataIndex === 'paid_on'">
                                {{ formatDate(record.paid_on) }}
                            </template>
                            <template v-if="column.dataIndex === 'next_payment_date'">
                                {{ formatDate(record.next_payment_date) }}
                            </template>

                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>

                            <template v-if="column.dataIndex === 'action'"> - </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted } from "vue";
import fields from "./fields";
import datatable from "../../../common/composable/datatable";
import common from "../../../common/composable/common";
import SuperAdminPageHeader from "../../layouts/SuperAdminPageHeader.vue";

export default {
    components: {
        SuperAdminPageHeader,
    },
    setup() {
        const { formatDate } = common();
        const { url, columns, filterableColumns } = fields();
        const { table, tableUrl, fetch, handleTableChange, onTableSearch } = datatable();

        onMounted(() => {
            setUrlData();
        });

        const setUrlData = () => {
            tableUrl.value = { url };
            table.filterableColumns = filterableColumns;

            fetch({
                page: 1,
            });
        };

        return {
            table,
            handleTableChange,
            onTableSearch,
            columns,
            filterableColumns,
            formatDate,
        };
    },
};
</script>

<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.online_orders`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.stock_management`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.online_orders`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-typography-text
                            type="warning"
                            strong
                            :style="{ fontSize: '18px' }"
                        >
                            {{ $t("online_orders.online_store_url") }}
                        </a-typography-text>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <a-input-group compact>
                            <a-input
                                v-model:value="storeUrl"
                                style="width: calc(100% - 100px)"
                            />
                            <router-link
                                :to="{
                                    name: 'front.homepage',
                                    params: { warehouse: selectedWarehouse.slug },
                                }"
                                target="_blank"
                            >
                                <a-button type="primary">
                                    {{ $t("common.view") }}
                                </a-button>
                            </router-link>
                        </a-input-group>
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                        <a-input-search
                            style="width: 100%"
                            v-model:value="filters.searchString"
                            show-search
                            :placeholder="
                                $t('common.placeholder_search_text', [
                                    $t('stock.invoice_number'),
                                ])
                            "
                        />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="6">
                        <a-select
                            v-model:value="filters.user_id"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t(`${orderPageObject.langKey}.user`),
                                ])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
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
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                        <DateRangePicker
                            ref="serachDateRangePicker"
                            @dateTimeChanged="
                                (changedDateTime) => (filters.dates = changedDateTime)
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs v-model:activeKey="filters.order_status">
                    <a-tab-pane
                        key="all"
                        :tab="`${$t('common.all')} ${$t('menu.online_orders')}`"
                    />

                    <a-tab-pane key="pending" :tab="$t('common.pending')" />
                    <a-tab-pane key="delivered" :tab="$t('common.delivered')" />
                    <a-tab-pane key="cancelled" :tab="$t('common.cancelled')" />
                </a-tabs>
            </a-col>
        </a-row>

        <OrderTable
            :orderType="orderType"
            :filters="filters"
            tableSize="middle"
            :bordered="true"
            :selectable="true"
        />
    </admin-page-table-content>
</template>

<script>
import { onMounted, watch, ref } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import common from "../../../../common/composable/common";
import OrderTable from "../../../components/order/OrderTable.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import InBetaMode from "./InBetaMode.vue";

export default {
    components: {
        PlusOutlined,
        OrderTable,
        DateRangePicker,
        AdminPageHeader,
        InBetaMode,
    },
    setup() {
        const {
            formatAmountCurrency,
            orderType,
            orderPageObject,
            orderStatus,
            permsArray,
            selectedWarehouse,
        } = common();
        const router = useRouter();
        const storeUrl = ref("");

        const users = ref([]);
        const serachDateRangePicker = ref(null);

        const filters = ref({
            payment_status: "all",
            user_id: undefined,
            dates: [],
            searchColumn: "invoice_number",
            searchString: "",
        });

        onMounted(() => {
            generateStorePath();
            const usersPromise = axiosAdmin.get(orderPageObject.value.userType);

            Promise.all([usersPromise]).then(([usersResponse]) => {
                users.value = usersResponse.data;
            });
        });

        const generateStorePath = () => {
            const storePathString = router.resolve({
                name: "front.homepage",
                params: { warehouse: selectedWarehouse.value.slug },
            });
            storeUrl.value = window.config.path + storePathString.href;
        };

        watch(selectedWarehouse, (newVal, oldVal) => {
            generateStorePath();

            filters.value = {
                order_status: "all",
                user_id: undefined,
                dates: [],
                searchColumn: "invoice_number",
                searchString: "",
            };

            serachDateRangePicker.value.resetPicker();
        });

        return {
            orderPageObject,

            permsArray,
            orderStatus,
            formatAmountCurrency,

            users,

            filters,
            orderType,
            serachDateRangePicker,

            selectedWarehouse,
            storeUrl,
        };
    },
};
</script>

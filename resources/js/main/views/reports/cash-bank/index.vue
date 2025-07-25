<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.cash_bank`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.cash_bank`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]" justify="end">
            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="4">
                <DateRangePicker
                    @dateTimeChanged="
                        (changedDateTime) => {
                            filters.dates = changedDateTime;
                        }
                    "
                />
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <a-row>
            <a-col :span="24">
                <a-tabs v-model:activeKey="activeType">
                    <a-tab-pane key="cash">
                        <template #tab>
                            <span>
                                <WalletOutlined />
                                {{ $t("payments.cash") }}
                            </span>
                        </template>
                    </a-tab-pane>
                    <a-tab-pane key="bank">
                        <template #tab>
                            <span>
                                <BankOutlined />
                                {{ $t("payments.bank") }}
                            </span>
                        </template>
                    </a-tab-pane>
                </a-tabs>
            </a-col>
        </a-row>

        <Payments :dates="filters.dates" :paymentMode="activeType" />
    </admin-page-table-content>
</template>

<script>
import { onMounted, onBeforeMount, ref, reactive } from "vue";
import { WalletOutlined, BankOutlined } from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import common from "../../../../common/composable/common";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import Payments from "../payments/Payments.vue";

export default {
    components: {
        AdminPageHeader,
        Payments,
        DateRangePicker,
        WalletOutlined,
        BankOutlined,
    },
    setup() {
        const { permsArray } = common();
        const activeType = ref("cash");
        const router = useRouter();
        const filters = reactive({
            dates: [],
        });

        onBeforeMount(() => {
            if (
                !(
                    permsArray.value.includes("customers_view") ||
                    permsArray.value.includes("suppliers_view") ||
                    permsArray.value.includes("admin")
                )
            ) {
                router.push("admin.dashboard.index");
            }
        });

        return {
            activeType,
            filters,
        };
    },
};
</script>

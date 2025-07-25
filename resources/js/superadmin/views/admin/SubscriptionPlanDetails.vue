<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.current_plan`)" style="padding: 0px" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.subscription`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.current_plan") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <SubscriptionSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-button
                            type="primary"
                            @click="
                                () =>
                                    $router.push({
                                        name: 'admin.subscription.change_plan',
                                    })
                            "
                        >
                            <FormOutlined />
                            {{ $t("subscription_plans.change_plan") }}
                        </a-button>
                    </a-col>
                </a-row>
            </admin-page-filters>

            <admin-page-table-content>
                <a-card
                    v-if="responseData && responseData.current_subscription_plan"
                    :title="
                        responseData &&
                        responseData.current_subscription_plan &&
                        responseData.current_subscription_plan.name
                            ? responseData.current_subscription_plan.name
                            : ''
                    "
                    class="page-content-container mt-20 mb-20 mt-20 mb-20"
                >
                    <a-row :gutter="16">
                        <a-col :span="12">
                            <a-typography-text strong>
                                {{ $t("subscription_plans.monthly_price") }}
                            </a-typography-text>
                        </a-col>
                        <a-col :span="12">
                            {{
                                formatAmountUsingCurrencyObject(
                                    responseData.current_subscription_plan.monthly_price,
                                    responseData.currency
                                )
                            }}
                        </a-col>
                    </a-row>
                    <a-row :gutter="16" class="mt-20">
                        <a-col :span="12">
                            <a-typography-text strong>
                                {{ $t("subscription_plans.annual_price") }}
                            </a-typography-text>
                        </a-col>
                        <a-col :span="12">
                            {{
                                formatAmountUsingCurrencyObject(
                                    responseData.current_subscription_plan.annual_price,
                                    responseData.currency
                                )
                            }}
                        </a-col>
                    </a-row>
                    <a-row :gutter="16" class="mt-20">
                        <a-col :span="12">
                            <a-typography-text strong>
                                {{ $t("subscription_plans.max_products") }}
                            </a-typography-text>
                        </a-col>
                        <a-col :span="12">
                            <span
                                v-if="
                                    responseData.current_subscription_plan.max_products ==
                                    0
                                "
                            >
                                {{ $t("subscription_plans.unlimited") }}
                            </span>
                            <span v-else>
                                {{ responseData.products_count }}
                                {{ $t("subscription_plans.out_of") }}
                                {{ responseData.current_subscription_plan.max_products }}
                            </span>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16" class="mt-20">
                        <a-col :span="12">
                            <a-typography-text strong>
                                {{ $t("payment_transaction.last_paid_on") }}
                            </a-typography-text>
                        </a-col>
                        <a-col :span="12">
                            <span v-if="responseData.last_payment_transcation">
                                {{
                                    formatDate(
                                        responseData.last_payment_transcation.paid_on
                                    )
                                }}
                            </span>
                            <span v-else>-</span>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16" class="mt-20">
                        <a-col :span="12">
                            <a-typography-text strong>
                                {{ $t("payment_transaction.next_payment_date") }}
                            </a-typography-text>
                        </a-col>
                        <a-col :span="12">
                            <span v-if="responseData.last_payment_transcation">
                                {{
                                    formatDate(
                                        responseData.last_payment_transcation
                                            .next_payment_date
                                    )
                                }}
                            </span>
                            <span v-else-if="appSetting.licence_expire_on != ''">
                                {{ formatDate(appSetting.licence_expire_on) }}
                            </span>
                            <span v-else>-</span>
                        </a-col>
                    </a-row>
                </a-card>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { ref, onMounted, reactive, toRef, watch } from "vue";
import { FormOutlined } from "@ant-design/icons-vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import SubscriptionSidebar from "./SubscriptionSidebar.vue";
import common from "../../../common/composable/common";

export default {
    components: {
        FormOutlined,
        AdminPageHeader,
        SubscriptionSidebar,
    },
    setup() {
        const responseData = ref([]);
        const { formatDate, formatAmountUsingCurrencyObject, appSetting } = common();

        onMounted(() => {
            axiosAdmin.get("subscription-plan-details").then((response) => {
                responseData.value = response.data;
            });
        });

        return {
            formatDate,
            formatAmountUsingCurrencyObject,
            responseData,

            appSetting,
        };
    },
};
</script>

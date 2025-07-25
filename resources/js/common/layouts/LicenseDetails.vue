<template>
    <template
        v-if="
            $route &&
            $route.meta &&
            $route.meta.menuKey &&
            $route.meta.menuKey != 'setup_company'
        "
    >
        <a-alert
            v-if="appSetting.status == 'license_expired'"
            :message="$t('subscription_plans.plan_expired')"
            :description="
                $t('subscription_plans.plan_expired_message', [
                    appSetting.licence_expire_on != ''
                        ? formatDate(appSetting.licence_expire_on)
                        : '',
                ])
            "
            type="error"
            show-icon
        >
            <template #icon>
                <CrownOutlined />
            </template>
        </a-alert>

        <a-alert
            v-else-if="
                appSetting.subscription_plan &&
                appSetting.subscription_plan.default == 'trial'
            "
            :message="$t('subscription_plans.trial_plan')"
            :description="
                $t('subscription_plans.trial_plan_message', [
                    appSetting.licence_expire_on != ''
                        ? formatDate(appSetting.licence_expire_on)
                        : '',
                ])
            "
            type="warning"
            show-icon
            @close="() => $router.push({ name: 'admin.subscription.change_plan' })"
        >
            <template #icon>
                <CrownOutlined />
            </template>
            <template
                v-if="
                    $route.meta &&
                    $route.meta.menuParent &&
                    $route.meta.menuParent != 'subscription'
                "
                #closeText
            >
                <a-button type="primary">
                    {{ $t("subscription_plans.change_plan") }}
                    <DoubleRightOutlined />
                </a-button>
            </template>
        </a-alert>
    </template>
</template>

<script>
import common from "../composable/common";
import { FormOutlined, CrownOutlined, DoubleRightOutlined } from "@ant-design/icons-vue";

export default {
    components: {
        FormOutlined,
        CrownOutlined,
        DoubleRightOutlined,
    },
    setup() {
        const { appSetting, formatDate } = common();

        return {
            appSetting,
            formatDate,
        };
    },
};
</script>

<style></style>

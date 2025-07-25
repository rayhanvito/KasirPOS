<template>
    <a-result v-if="loading || completed">
        <template #icon> </template>
        <template #title>
            <span :style="{ color: messageColor }">
                {{ message }}
            </span>
        </template>
        <template #extra>
            <SyncOutlined
                v-if="!completed"
                :style="{ fontSize: '38px', color: '#5254cf' }"
                spin
            />
        </template>
    </a-result>
    <div v-show="!loading && !completed" ref="paypalForm"></div>
</template>

<script>
import { ref, onMounted } from "vue";
import { SyncOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

export default {
    props: ["paymentMethod", "subscribePlan", "planType"],
    emits: ["success"],
    components: {
        SyncOutlined,
    },
    setup(props, { emit }) {
        const router = useRouter();
        const { t } = useI18n();
        const paypalForm = ref(null);
        const loading = ref(true);
        const completed = ref(false);
        const planId = ref("");
        const message = ref(t("subscription_plans.processing_payment"));
        const messageColor = ref("#7676e3");

        onMounted(() => {
            axiosAdmin
                .get(`/paypal/${props.subscribePlan.xid}/${props.planType}`)
                .then((response) => {
                    console.log(response.data.id);
                    planId.value = response.data.id;
                    const script = document.createElement("script");
                    script.setAttribute(
                        "src",
                        `https://www.paypal.com/sdk/js?client-id=${props.paymentMethod.credentials.paypal_client_id}&vault=true&intent=subscription`
                    );
                    script.addEventListener("load", paypalLoaded);
                    script.async = true;
                    document.head.appendChild(script);
                });
        });

        const paypalLoaded = () => {
            loading.value = false;

            window.paypal
                .Buttons({
                    createSubscription: function (data, actions) {
                        return actions.subscription.create({
                            plan_id: planId.value,
                        });
                    },
                    onApprove: function (data, actions) {
                        loading.value = true;

                        axiosAdmin
                            .post(`/paypal-recurring`, {
                                paypal_subscription_id: data.subscriptionID,
                                paypal_order_id: data.orderID,
                            })
                            .then((response) => {
                                if (response.data.status == "success") {
                                    message.value = t(
                                        "subscription_plans.payment_success"
                                    );
                                    messageColor.value = "#7676e3";

                                    router.push({
                                        name: "admin.subscription.change_plan",
                                    });
                                } else {
                                    message.value = t(
                                        "subscription_plans.payment_failed"
                                    );
                                    messageColor.value = "#ff4d4f";
                                }

                                loading.value = false;
                                completed.value = true;
                            })
                            .catch((errorResponse) => {
                                message.value = t("subscription_plans.payment_failed");
                                messageColor.value = "#ff4d4f";

                                loading.value = false;
                                completed.value = true;
                            });
                    },
                })
                .render(paypalForm.value);
        };

        return {
            paypalForm,
            loading,
            completed,
            message,
            messageColor,
        };
    },
};
</script>

<template>
    <a-alert
        :message="$t('setup_company.setup_not_completed')"
        :description="$t('setup_company.setup_not_completed_description')"
        type="error"
        show-icon
    />
    <a-card class="setup-page-content-container">
        <a-row class="mt-40">
            <a-col :span="20" :offset="1">
                <a-steps :current="currentStep">
                    <a-step :status="warehouseStepStatus">
                        <template #title>
                            {{ $t("setup_company.warehouse") }}
                        </template>
                        <template #description>
                            <span>{{ $t("setup_company.add_first_warehouse") }}</span>
                        </template>
                    </a-step>
                    <a-step :status="currencyStepStatus">
                        <template #title>
                            {{ $t("setup_company.currency") }}
                        </template>
                        <template #description>
                            <span>{{ $t("setup_company.add_first_currency") }}</span>
                        </template>
                    </a-step>
                    <a-step :status="paymentModeStepStatus">
                        <template #title>
                            {{ $t("setup_company.payment_mode") }}
                        </template>
                        <template #description>
                            <span>{{ $t("setup_company.add_payment_mode") }}</span>
                        </template>
                    </a-step>
                    <a-step :status="companySettingStepStatus">
                        <template #title>
                            {{ $t("setup_company.company_settings") }}
                        </template>
                        <template #description>
                            <span>{{
                                $t("setup_company.set_company_basic_settings")
                            }}</span>
                        </template>
                    </a-step>
                </a-steps>
                <a-divider></a-divider>

                <template v-if="appDetails && appDetails.app && appDetails.app.name">
                    <template v-if="currentStep == 0">
                        <a-button type="primary" @click="addWarehouse">
                            <PlusOutlined />
                            {{ $t("warehouse.add") }}
                        </a-button>

                        <WarehouseTable
                            class="mb-40"
                            ref="warhouseTableRef"
                            :showFilter="false"
                            @addOrEditSuccess="warehouseAddSuccess"
                        />
                    </template>
                    <template v-if="currentStep == 1">
                        <a-button type="primary" @click="addCurrency">
                            <PlusOutlined />
                            {{ $t("currency.add") }}
                        </a-button>

                        <CurrencyTable
                            class="mb-40"
                            ref="currencyTableRef"
                            :showFilter="false"
                            @addOrEditSuccess="currencyAddSuccess"
                        />
                    </template>
                    <template v-if="currentStep == 2">
                        <a-button type="primary" @click="addPaymentMode">
                            <PlusOutlined />
                            {{ $t("payment_mode.add") }}
                        </a-button>

                        <PaymentModeTable
                            class="mb-40"
                            ref="paymentModeTableRef"
                            :showFilter="false"
                            @addOrEditSuccess="paymentModeAddSuccess"
                        />
                    </template>
                    <template v-if="currentStep == 3">
                        <EditSetupSetting
                            class="mb-40"
                            ref="companySettingRef"
                            :showSubmitButton="false"
                            @updateSuccess="companySettingUpdated"
                        />
                    </template>
                </template>
            </a-col>
        </a-row>
    </a-card>
    <a-row>
        <a-col :span="20" :offset="1">
            <div
                :style="{
                    position: 'absolute',
                    right: 0,
                    bottom: 0,
                    width: '100%',
                    borderTop: '1px solid #e9e9e9',
                    padding: '10px 16px',
                    background: '#fff',
                    textAlign: 'left',
                    zIndex: 1,
                    marginBottom: '10px',
                }"
            >
                <a-space>
                    <a-button
                        v-if="currentStep >= 1 && currentStep != 3"
                        type="primary"
                        :style="{ backgroundColor: '#faad14', border: '#faad14' }"
                        @click="goBack"
                    >
                        <DoubleLeftOutlined />
                        {{ $t("setup_company.previous_step") }}
                    </a-button>
                    <a-button
                        v-if="currentStep < 3"
                        type="primary"
                        @click="goNext"
                        :disabled="currentStep >= correctStep"
                    >
                        {{ $t("setup_company.next_step") }}
                        <DoubleRightOutlined />
                    </a-button>
                    <a-button
                        v-if="currentStep == 3 && companySettingRef"
                        type="primary"
                        :style="{ backgroundColor: '#52c41a', border: '#52c41a' }"
                        @click="submitCompanySetting"
                        :loading="companySettingRef.loading"
                    >
                        <SaveOutlined />
                        {{ $t("setup_company.save_finish_setup") }}
                    </a-button>
                </a-space>
            </div>
        </a-col>
    </a-row>

    <a-modal
        v-model:open="showFinalModal"
        :maskClosable="false"
        :closable="false"
        :footer="null"
        centered
    >
        <a-result>
            <template #title>
                <span v-if="setupCompleted" :style="{ color: '#7676e3' }">
                    {{ $t("setup_company.setup_complete_message") }}
                </span>
                <span v-else :style="{ color: '#7676e3' }">
                    {{ $t("setup_company.setup_running_message") }}
                </span>
            </template>
            <template #icon>
                <CheckCircleOutlined v-if="setupCompleted" />
                <InfoCircleOutlined v-else />
            </template>
            <template #extra>
                <a-button
                    v-if="setupCompleted"
                    type="primary"
                    :style="{ backgroundColor: '#52c41a', border: '#52c41a' }"
                    @click="goToDashboard"
                >
                    <HomeOutlined />
                    {{ $t("setup_company.go_to_dashboard") }}
                </a-button>
                <SyncOutlined
                    v-else
                    :style="{ fontSize: '38px', color: '#5254cf' }"
                    spin
                />
            </template>
        </a-result>
    </a-modal>
</template>

<script>
import { defineComponent, ref, onMounted, reactive, toRefs } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    DoubleRightOutlined,
    DoubleLeftOutlined,
    SaveOutlined,
    SyncOutlined,
    CheckCircleOutlined,
    HomeOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import common from "../../common/composable/common";
import WarehouseTable from "./settings/warehouses/WarehouseTable.vue";
import CurrencyTable from "./common/settings/currency/CurrencyTable.vue";
import PaymentModeTable from "../views/settings/payment-modes/PaymentModeTable.vue";
import EditSetupSetting from "../views/settings/company/EditSetupSetting.vue";

export default defineComponent({
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        DoubleRightOutlined,
        DoubleLeftOutlined,
        SaveOutlined,
        SyncOutlined,
        CheckCircleOutlined,
        HomeOutlined,
        InfoCircleOutlined,

        WarehouseTable,
        CurrencyTable,
        PaymentModeTable,
        EditSetupSetting,
    },
    setup() {
        const { permsArray, appSetting } = common();
        const router = useRouter();
        const store = useStore();
        const warhouseTableRef = ref(null);
        const currencyTableRef = ref(null);
        const paymentModeTableRef = ref(null);
        const companySettingRef = ref(null);
        const appDetails = ref([]);
        const currentStep = ref(0);
        const correctStep = ref(0);
        const stepStatus = reactive({
            warehouseStepStatus: "wait",
            currencyStepStatus: "wait",
            paymentModeStepStatus: "wait",
            companySettingStepStatus: "wait",
        });
        const showFinalModal = ref(false);
        const setupCompleted = ref(false);

        onMounted(() => {
            checkStepNumber();
        });

        const checkStepNumber = () => {
            axiosAdmin.get("app").then((appResponse) => {
                appDetails.value = appResponse.data;

                // For Setting Status
                stepStatus.warehouseStepStatus =
                    appDetails.value.total_warehouses > 0 ? "finish" : "wait";
                stepStatus.currencyStepStatus =
                    appDetails.value.total_currencies > 0 ? "finish" : "wait";
                stepStatus.paymentModeStepStatus =
                    appDetails.value.total_payment_modes > 0 ? "finish" : "wait";
                stepStatus.companySettingStepStatus =
                    companySettingNotSetup() == false ? "finish" : "wait";

                // For Setting Step
                if (appDetails.value.total_warehouses == 0) {
                    currentStep.value = 0;
                    correctStep.value = 0;
                    stepStatus.warehouseStepStatus = "error";
                } else if (appDetails.value.total_currencies == 0) {
                    currentStep.value = 1;
                    correctStep.value = 1;
                    stepStatus.currencyStepStatus = "error";
                } else if (appDetails.value.total_payment_modes == 0) {
                    currentStep.value = 2;
                    correctStep.value = 2;
                    stepStatus.paymentModeStepStatus = "error";
                } else if (companySettingNotSetup()) {
                    currentStep.value = 3;
                    correctStep.value = 3;
                    stepStatus.companySettingStepStatus = "error";
                } else {
                    currentStep.value = 3;
                    correctStep.value = 3;

                    setupCompleted.value = true;
                    showFinalModal.value = true;
                }
            });
        };

        const companySettingNotSetup = () => {
            return appDetails.value.app.x_warehouse_id == null ||
                appDetails.value.app.x_currency_id == null
                ? true
                : false;
        };

        const addWarehouse = () => {
            warhouseTableRef.value.addItem();
        };

        const warehouseAddSuccess = (details) => {
            checkStepNumber();
        };

        const addCurrency = () => {
            currencyTableRef.value.addItem();
        };

        const currencyAddSuccess = (details) => {
            checkStepNumber();
        };

        const addPaymentMode = () => {
            paymentModeTableRef.value.addItem();
        };

        const paymentModeAddSuccess = (details) => {
            checkStepNumber();
        };

        const submitCompanySetting = () => {
            companySettingRef.value.onSubmit();
        };

        const companySettingUpdated = () => {
            showFinalModal.value = true;

            axiosAdmin
                .post(`/user`)
                .then(function (response) {
                    store.commit("auth/updateUser", response.data.user);

                    axiosAdmin
                        .post("change-warehouse", {
                            warehouse_id: response.data.user.x_warehouse_id,
                        })
                        .then((response) => {
                            store.commit("auth/updateWarehouse", response.data.warehouse);
                            setupCompleted.value = true;
                        });
                })
                .catch(function (error) {});
        };

        const goToDashboard = () => {
            showFinalModal.value = false;

            router.push({
                name: "admin.dashboard.index",
            });
        };

        const goBack = () => {
            currentStep.value = currentStep.value == 0 ? 0 : currentStep.value - 1;
        };

        const goNext = () => {
            currentStep.value = currentStep.value >= 3 ? 3 : currentStep.value + 1;
        };

        return {
            appDetails,
            currentStep,
            correctStep,
            ...toRefs(stepStatus),

            warhouseTableRef,
            addWarehouse,
            warehouseAddSuccess,
            currencyTableRef,
            addCurrency,
            currencyAddSuccess,
            paymentModeTableRef,
            addPaymentMode,
            paymentModeAddSuccess,
            companySettingRef,
            submitCompanySetting,
            companySettingUpdated,
            showFinalModal,
            setupCompleted,
            goToDashboard,

            goBack,
            goNext,
        };
    },
});
</script>

<style>
.setup-page-content-container {
    min-height: calc(100vh - 147px);
}
</style>

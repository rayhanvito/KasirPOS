<template>
    <a-drawer
        :title="$t('payments.order_payment')"
        :width="drawerWidth"
        :maskClosable="false"
        :open="visible"
        @close="drawerClosed"
    >
        <a-row>
            <a-col :xs="24" :sm="24" :md="8" :lg="8">
                <a-row>
                    <a-col :span="24">
                        <a-statistic
                            :title="$t('stock.total_items')"
                            :value="selectedProducts.length"
                            style="margin-right: 50px"
                        />
                    </a-col>
                    <a-col :span="24" class="mt-20">
                        <a-statistic
                            :title="$t('stock.paying_amount')"
                            :value="formatAmountCurrency(totalEnteredAmount)"
                        />
                    </a-col>
                    <a-col :span="24" class="mt-20">
                        <a-statistic
                            :title="$t('stock.payable_amount')"
                            :value="formatAmountCurrency(data.subtotal)"
                        />
                    </a-col>
                    <a-col :span="24" class="mt-20">
                        <a-statistic
                            v-if="totalEnteredAmount <= data.subtotal"
                            :title="$t('payments.due_amount')"
                            :value="
                                formatAmountCurrency(data.subtotal - totalEnteredAmount)
                            "
                        />
                        <a-statistic
                            v-else
                            :title="$t('stock.change_return')"
                            :value="
                                formatAmountCurrency(totalEnteredAmount - data.subtotal)
                            "
                        />
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="24" :sm="24" :md="16" :lg="16">
                <a-row :gutter="[24, 24]">
                    <a-col :span="24" v-if="!showAddForm">
                        <a-row :gutter="[16, 8]" class="mt-20">
                            <a-col :xs="24" :sm="24" :md="10" :lg="10">
                                <a-button
                                    :block="true"
                                    type="primary"
                                    @click="() => (showAddForm = true)"
                                >
                                    <PlusOutlined />
                                    {{ $t("payments.add") }}
                                </a-button>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="10" :lg="10">
                                <a-button :block="true" @click="completeOrder">
                                    {{ $t("stock.complete_order") }}
                                    <RightOutlined />
                                </a-button>
                            </a-col>
                        </a-row>
                    </a-col>
                    <a-col :span="24" v-else>
                        <a-row>
                            <a-col :xs="24" :sm="24" :md="10" :lg="10">
                                <a-button :block="true" type="primary" @click="goBack">
                                    <LeftOutlined />
                                    {{ $t("common.back") }}
                                </a-button>
                            </a-col>
                        </a-row>
                    </a-col>
                    <a-col :span="24" v-if="!showAddForm">
                        <a-table
                            :dataSource="allPaymentRecords"
                            :columns="paymentRecordsColumns"
                            :pagination="false"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'payment_mode'">
                                    {{ getPaymentModeName(record.payment_mode_id) }}
                                </template>
                                <template v-if="column.dataIndex === 'amount'">
                                    {{ formatAmountCurrency(record.amount) }}
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button
                                        type="primary"
                                        @click="deletePayment(record.id)"
                                        danger
                                    >
                                        <template #icon><DeleteOutlined /></template>
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </a-col>
                    <a-col :span="24" v-else>
                        <a-form layout="vertical">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('payments.payment_mode')"
                                        name="payment_mode_id"
                                        :help="
                                            rules.payment_mode_id
                                                ? rules.payment_mode_id.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.payment_mode_id ? 'error' : null
                                        "
                                    >
                                        <a-select
                                            v-model:value="formData.payment_mode_id"
                                            :placeholder="
                                                $t('common.select_default_text', [
                                                    $t('payments.payment_mode'),
                                                ])
                                            "
                                            :allowClear="true"
                                        >
                                            <a-select-option
                                                v-for="paymentMode in paymentModes"
                                                :key="paymentMode.xid"
                                                :value="paymentMode.xid"
                                            >
                                                {{ paymentMode.name }}
                                            </a-select-option>
                                        </a-select>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('stock.paying_amount')"
                                        name="amount"
                                        :help="rules.amount ? rules.amount.message : null"
                                        :validateStatus="rules.amount ? 'error' : null"
                                    >
                                        <a-input
                                            :prefix="appSetting.currency.symbol"
                                            v-model:value="formData.amount"
                                            :placeholder="
                                                $t('common.placeholder_default_text', [
                                                    $t('stock.payable_amount'),
                                                ])
                                            "
                                        />
                                        <small style="color: #7c8db5 !important">
                                            {{ $t("stock.payable_amount") }}
                                            <span>
                                                {{ formatAmountCurrency(data.subtotal) }}
                                            </span>
                                        </small>
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-form-item
                                        :label="$t('payments.notes')"
                                        name="notes"
                                        :help="rules.notes ? rules.notes.message : null"
                                        :validateStatus="rules.notes ? 'error' : null"
                                    >
                                        <a-textarea
                                            v-model:value="formData.notes"
                                            :placeholder="$t('payments.notes')"
                                            :rows="5"
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-button
                                        type="primary"
                                        :loading="loading"
                                        @click="onSubmit"
                                        block
                                    >
                                        <template #icon>
                                            <CheckOutlined />
                                        </template>
                                        {{ $t("common.add") }}
                                    </a-button>
                                </a-col>
                            </a-row>
                        </a-form>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </a-drawer>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import {
    CheckOutlined,
    PlusOutlined,
    LeftOutlined,
    RightOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { find, filter, sumBy } from "lodash-es";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";

export default {
    props: ["visible", "data", "selectedProducts"],
    emits: ["closed", "success"],
    components: {
        CheckOutlined,
        PlusOutlined,
        LeftOutlined,
        RightOutlined,
        DeleteOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { appSetting, formatAmountCurrency } = common();
        const paymentModes = ref([]);
        const formData = ref({
            payment_mode_id: undefined,
            amount: 0,
            notes: "",
        });
        const { t } = useI18n();
        const allPaymentRecords = ref([]);
        const paymentRecordsColumns = ref([
            {
                title: t("payments.payment_mode"),
                dataIndex: "payment_mode",
            },
            {
                title: t("payments.amount"),
                dataIndex: "amount",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ]);
        const showAddForm = ref(false);

        onMounted(() => {
            axiosAdmin.get("payment-modes").then((response) => {
                paymentModes.value = response.data;
            });
        });

        const drawerClosed = () => {
            formData.value = {
                payment_mode_id: undefined,
                amount: 0,
                notes: "",
            };
            allPaymentRecords.value = [];
            emit("closed");
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "pos/payment",
                data: formData.value,
                success: (res) => {
                    allPaymentRecords.value = [
                        ...allPaymentRecords.value,
                        { ...formData.value, id: Math.random().toString(36).slice(2) },
                    ];

                    formData.value = {
                        payment_mode_id: undefined,
                        amount: 0,
                        notes: "",
                    };

                    showAddForm.value = false;
                },
            });
        };

        const completeOrder = () => {
            const newFormDataObject = {
                all_payments: allPaymentRecords.value,
                product_items: props.selectedProducts,
                details: props.data,
            };

            addEditRequestAdmin({
                url: "pos/save",
                data: newFormDataObject,
                successMessage: props.successMessage,
                success: (res) => {
                    formData.value = {
                        payment_mode_id: undefined,
                        amount: 0,
                        notes: "",
                    };

                    allPaymentRecords.value = [];
                    showAddForm.value = false;
                    emit("success", res.order);
                },
            });
        };

        const goBack = () => {
            formData.value = {
                payment_mode_id: undefined,
                amount: 0,
                notes: "",
            };

            showAddForm.value = false;
        };

        const getPaymentModeName = (paymentId) => {
            var selectedMode = find(paymentModes.value, ["xid", paymentId]);

            return selectedMode ? selectedMode.name : "-";
        };

        const deletePayment = (paymentId) => {
            var newResult = filter(allPaymentRecords.value, (newPaymentMode) => {
                return newPaymentMode.id != paymentId;
            });

            allPaymentRecords.value = newResult;
        };

        const totalEnteredAmount = computed(() => {
            var allPaymentSum = sumBy(allPaymentRecords.value, (newPaymentAmount) => {
                return parseFloat(newPaymentAmount.amount);
            });

            return allPaymentSum + parseFloat(formData.value.amount);
        });

        return {
            loading,
            rules,
            drawerClosed,
            paymentModes,
            formData,
            appSetting,
            formatAmountCurrency,
            onSubmit,

            allPaymentRecords,
            paymentRecordsColumns,
            showAddForm,
            completeOrder,
            goBack,
            getPaymentModeName,
            deletePayment,
            totalEnteredAmount,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "50%",
        };
    },
};
</script>

<style></style>

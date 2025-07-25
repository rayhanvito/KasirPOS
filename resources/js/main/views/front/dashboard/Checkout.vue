<template>
    <div class="mt-30 mb-30">
        <a-row type="flex" justify="center">
            <a-col :span="20">
                <a-breadcrumb>
                    <a-breadcrumb-item>
                        <router-link
                            :to="{
                                name: 'front.homepage',
                                params: { warehouse: frontWarehouse.slug },
                            }"
                        >
                            {{ $t("front.home") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>
                        <router-link
                            :to="{
                                name: 'front.dashboard',
                                params: { warehouse: frontWarehouse.slug },
                            }"
                        >
                            {{ $t("front.dashboard") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>
                        <router-link
                            :to="{
                                name: 'front.orders',
                                params: { warehouse: frontWarehouse.slug },
                            }"
                        >
                            {{ $t("front.my_orders") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>{{ $t("front.checkout_page") }}</a-breadcrumb-item>
                </a-breadcrumb>

                <a-row :gutter="[30, 30]" class="mt-30">
                    <a-col :xs="24" :sm="24" :md="24" :lg="16" :xl="16">
                        <a-card
                            :title="null"
                            :bordered="false"
                            :style="{ borderRadius: '10px' }"
                            class="dashboard-container"
                        >
                            <a-row>
                                <a-col :span="24">
                                    <a-alert
                                        v-if="selectedAddress == null"
                                        :message="$t('front.select_shipping_address')"
                                        type="error"
                                        class="mb-10"
                                        show-icon
                                    />

                                    <a-typography-title :level="3">
                                        1. {{ $t("front.address_details") }}
                                    </a-typography-title>
                                    <UserAddress @onAddressSelection="addressSelected" />
                                    <a-divider />

                                    <a-typography-title :level="3">
                                        2. {{ $t("front.payment_details") }}
                                    </a-typography-title>
                                    <a-row :gutter="30" class="mt-20">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="24"
                                            :lg="12"
                                            :xl="12"
                                        >
                                            <div class="payment-methods">
                                                <div class="cod">
                                                    <div class="icon-text">
                                                        <wallet-outlined class="mr-5" />
                                                        {{ $t("front.cash_on_delivery") }}
                                                    </div>
                                                    <a-radio
                                                        v-model:checked="addressMethod"
                                                    >
                                                    </a-radio>
                                                </div>
                                            </div>
                                        </a-col>
                                    </a-row>
                                    <a-divider />

                                    <a-row :gutter="[30, 10]" class="mt-40">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="24"
                                            :lg="12"
                                            :xl="12"
                                        >
                                            <a-button type="primary" size="large" block>
                                                <router-link
                                                    :to="{
                                                        name: 'front.homepage',
                                                        params: {
                                                            warehouse:
                                                                frontWarehouse.slug,
                                                        },
                                                    }"
                                                >
                                                    <RollbackOutlined />
                                                    {{ $t("front.continue_shopping") }}
                                                </router-link>
                                            </a-button>
                                        </a-col>
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="24"
                                            :lg="12"
                                            :xl="12"
                                        >
                                            <a-button
                                                type="primary"
                                                size="large"
                                                @click="confirmOrder"
                                                :disabled="
                                                    products.length == 0 ||
                                                    selectedAddress == null
                                                "
                                                :loading="loading"
                                                block
                                            >
                                                {{ $t("front.confirm_order") }}
                                                <RightOutlined />
                                            </a-button>
                                        </a-col>
                                    </a-row>
                                </a-col>
                            </a-row>
                        </a-card>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                        <a-card
                            :title="null"
                            :bordered="false"
                            :style="{ borderRadius: '10px' }"
                        >
                            <a-list
                                class="demo-loadmore-list"
                                item-layout="horizontal"
                                :data-source="products"
                            >
                                <template #renderItem="{ item }">
                                    <a-list-item>
                                        <a-list-item-meta>
                                            <template #title>
                                                {{ item.name }}
                                                <br />
                                                <small
                                                    :style="{
                                                        color: 'rgba(0, 0, 0, 0.45)',
                                                    }"
                                                >
                                                    Price:
                                                    {{
                                                        formatAmountCurrency(
                                                            getSalesPriceWithTax(item)
                                                        )
                                                    }}
                                                </small>
                                            </template>
                                            <template #avatar>
                                                <a-avatar
                                                    :src="item.image_url"
                                                    size="large"
                                                />
                                            </template>
                                            <template #description>
                                                {{
                                                    formatAmountCurrency(
                                                        getSalesPriceWithTax(item) *
                                                            item.cart_quantity
                                                    )
                                                }}
                                            </template>
                                        </a-list-item-meta>
                                        <div>
                                            <a-input-number
                                                v-model:value="item.cart_quantity"
                                                :min="1"
                                                :style="{ width: '60px' }"
                                                @change="updateCart"
                                            />
                                        </div>
                                        <template #actions>
                                            <a-button
                                                type="link"
                                                @click="removeItem(item.xid)"
                                            >
                                                <delete-outlined
                                                    :style="{
                                                        fontSize: '20px',
                                                        color: '#f87171',
                                                    }"
                                                />
                                            </a-button>
                                        </template>
                                    </a-list-item>
                                </template>
                            </a-list>

                            <a-divider />
                            <div class="item-total pd-10">
                                <a-row class="mt-10">
                                    <a-col :span="12">{{
                                        $t("stock.grand_total")
                                    }}</a-col>
                                    <a-col :span="12" class="text-right">
                                        {{ formatAmountCurrency(total) }}
                                    </a-col>
                                </a-row>
                            </div>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, createVNode } from "vue";
import {
    DeleteOutlined,
    WalletOutlined,
    RollbackOutlined,
    RightOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import cart from "../../../../common/composable/cart";
import UserAddress from "./address/Index.vue";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";
import apiFront from "../../../../common/composable/apiFront";

export default defineComponent({
    components: {
        DeleteOutlined,
        WalletOutlined,
        RollbackOutlined,
        RightOutlined,
        UserAddress,
        ExclamationCircleOutlined,
    },
    setup() {
        const {
            products,
            updateCart,
            removeItem,
            subtotal,
            totalTax,
            total,
            formatAmountCurrency,
            frontWarehouse,
        } = cart();
        const selectedAddress = ref(null);
        const addressMethod = ref(true);
        const { t } = useI18n();
        const router = useRouter();
        const store = useStore();
        const { loading, addEditRequestFront } = apiFront();

        onMounted(() => {});

        const addressSelected = (addressId) => {
            selectedAddress.value = addressId;
        };

        const confirmOrder = () => {
            Modal.confirm({
                title: t("front.confirm_order"),
                icon: createVNode(ExclamationCircleOutlined),
                content: t("front.confirm_order_message"),
                okText: t("common.yes"),
                cancelText: t("common.no"),
                onOk() {
                    addEditRequestFront({
                        url: `front/self/checkout-orders/${frontWarehouse.value.slug}`,
                        data: {
                            products: products.value,
                            address_id: selectedAddress.value,
                            warehouse: frontWarehouse.value.slug,
                        },
                        successMessage: t("front.order_placed_message"),
                        success: (res) => {
                            store.commit("front/addCartItems", []);
                            router.push({
                                name: "front.checkout.success",
                                params: {
                                    uniqueId: res.unique_id,
                                    warehouse: frontWarehouse.value.slug,
                                },
                            });
                        },
                    });
                },
                onCancel() {
                    loading.value = false;
                },
            });
        };

        return {
            products,
            removeItem,
            updateCart,
            formatAmountCurrency,
            subtotal,
            total,
            totalTax,
            selectedAddress,
            addressSelected,
            addressMethod,
            getSalesPriceWithTax,
            confirmOrder,
            loading,
            frontWarehouse,
        };
    },
});
</script>
<style lang="less">
.payment-methods {
    border: 1px solid #f0f2f5;
    border-radius: 5px;
    background: #fbfbfb;
    padding: 10px;

    .cod {
        display: flex;
        justify-content: space-between;

        .icon-text {
            display: flex;
            align-items: center;
            font-weight: 500;
        }
    }
}
</style>

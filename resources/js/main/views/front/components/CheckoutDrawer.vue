<template>
    <a-button type="link" @click="showDrawer">
        <a-badge :count="totalCartItems">
            <shopping-cart-outlined
                :style="{
                    fontSize: '24px',
                    color: '#fff',
                    verticalAlign: 'middle',
                }"
            />
        </a-badge>
    </a-button>
    <a-drawer
        v-model:open="visible"
        :width="innerWidth <= 768 ? '80%' : 500"
        placement="right"
        :closable="false"
        :headerStyle="{ backgroundColor: '#eef2ff' }"
    >
        <template #title>
            <shopping-outlined :style="{ fontSize: '20px' }" />
            Shopping Cart
        </template>
        <template #extra>
            <a-button type="link" @click="closeDrawer">
                <close-outlined :style="{ fontSize: '14px' }" />
            </a-button>
        </template>

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
                            <small :style="{ color: 'rgba(0, 0, 0, 0.45)' }">
                                Price:
                                {{ formatAmountCurrency(getSalesPriceWithTax(item)) }}
                            </small>
                        </template>
                        <template #avatar>
                            <a-avatar :src="item.image_url" size="large" />
                        </template>
                        <template #description>
                            {{
                                formatAmountCurrency(
                                    getSalesPriceWithTax(item) * item.cart_quantity
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
                        <a-button type="link" @click="removeItem(item.xid)">
                            <delete-outlined
                                :style="{ fontSize: '20px', color: '#f87171' }"
                            />
                        </a-button>
                    </template>
                </a-list-item>
            </template>
        </a-list>

        <template #footer>
            <a-row type="flex" justify="start">
                <a-col :span="12">
                    <a-typography-title :level="5">
                        Subtotal: {{ formatAmountCurrency(total) }}
                    </a-typography-title>
                </a-col>
                <a-col :span="12">
                    <a-button type="primary" @click="proceedCheckout" block>
                        Checkout
                        <right-outlined />
                    </a-button>
                </a-col>
            </a-row>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, computed, ref, onMounted, watch } from "vue";
import {
    ShoppingCartOutlined,
    ShoppingOutlined,
    CloseOutlined,
    DeleteOutlined,
    RightOutlined,
} from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import cart from "../../../..//common/composable/cart";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default defineComponent({
    emits: ["openLoginModal"],
    components: {
        ShoppingCartOutlined,
        ShoppingOutlined,
        CloseOutlined,
        DeleteOutlined,
        RightOutlined,
    },
    setup(props, { emit }) {
        const store = useStore();
        const visible = ref(false);
        const router = useRouter();
        const {
            products,
            updateCart,
            removeItem,
            total,
            formatAmountCurrency,
            frontWarehouse,
        } = cart();

        const showDrawer = () => (visible.value = true);

        const closeDrawer = () => (visible.value = false);

        const isLoggedIn = computed(() => {
            return store.getters["front/isLoggedIn"];
        });

        const proceedCheckout = () => {
            visible.value = false;

            if (isLoggedIn.value) {
                router.push({
                    name: "front.checkout",
                    params: { warehouse: frontWarehouse.value.slug },
                });
            } else {
                emit("openLoginModal");
            }
        };

        return {
            visible,
            showDrawer,
            closeDrawer,
            totalCartItems: computed(() => store.getters["front/totalCartItems"]),

            products,
            removeItem,
            updateCart,
            formatAmountCurrency,
            total,
            proceedCheckout,
            getSalesPriceWithTax,
            frontWarehouse,

            innerWidth: window.innerWidth,
        };
    },
});
</script>

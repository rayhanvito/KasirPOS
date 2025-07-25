<template>
    <div class="product" v-if="currentProduct && currentProduct.xid">
        <div class="product-top">
            <a href="javascript:void(0)" @click="showModal">
                <img :src="currentProduct.image_url" class="img-fit" />
            </a>
        </div>
        <div class="product-bottom">
            <div>
                <span class="product-category">{{ currentProduct.category.name }}</span>
                <h5 class="product-title">
                    {{ currentProduct.name }}
                </h5>
            </div>
            <div class="product-details">
                <div class="product-price">
                    <span>{{
                        formatAmountCurrency(getSalesPriceWithTax(currentProduct))
                    }}</span>
                    <br />
                    <del>{{ formatAmountCurrency(currentProduct.details.mrp) }}</del>
                </div>
                <div class="product-card-add-edit">
                    <a-button-group v-if="currentProduct.cart_quantity > 0">
                        <a-button
                            @click="
                                currentProduct.cart_quantity -= 1;
                                addItem(currentProduct);
                            "
                            size="small"
                        >
                            <minus-outlined />
                        </a-button>
                        <a-button size="small">
                            {{ currentProduct.cart_quantity }}
                        </a-button>
                        <a-button
                            @click="
                                currentProduct.cart_quantity += 1;
                                addItem(currentProduct);
                            "
                            size="small"
                        >
                            <plus-outlined />
                        </a-button>
                    </a-button-group>
                    <a-button
                        v-else
                        @click="
                            currentProduct.cart_quantity++;
                            addItem(currentProduct);
                        "
                        type="primary"
                    >
                        <template #icon>
                            <ShoppingCartOutlined />
                        </template>
                    </a-button>
                </div>
            </div>
        </div>
    </div>

    <a-modal v-model:open="visible" centered :footer="null" :title="null" :width="850">
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <div class="product-details-image">
                    <span>
                        <img :src="currentProduct.image_url" />
                    </span>
                </div>
            </a-col>
            <a-col :span="12">
                <a-typography-title :level="3">{{
                    currentProduct.name
                }}</a-typography-title>
                <div>
                    <a-tag v-if="currentProduct.category_id" color="purple">
                        {{ currentProduct.category.name }}
                    </a-tag>
                    <a-tag v-if="currentProduct.brand_id" color="cyan">
                        {{ currentProduct.brand.name }}
                    </a-tag>
                </div>
                <p class="mt-10" v-if="currentProduct.description">
                    {{ currentProduct.description }}
                </p>
                <a-typography-title class="mt-20" :level="3">
                    {{ formatAmountCurrency(getSalesPriceWithTax(currentProduct)) }}
                </a-typography-title>
                <div class="mt-10 mb-20">
                    <a-button-group v-if="currentProduct.cart_quantity > 0">
                        <a-button
                            @click="
                                currentProduct.cart_quantity -= 1;
                                addItem(currentProduct);
                            "
                            size="large"
                        >
                            <minus-outlined />
                        </a-button>
                        <a-button size="large">
                            {{ currentProduct.cart_quantity }}
                        </a-button>
                        <a-button
                            @click="
                                currentProduct.cart_quantity += 1;
                                addItem(currentProduct);
                            "
                            size="large"
                        >
                            <plus-outlined />
                        </a-button>
                    </a-button-group>
                    <a-button
                        v-else
                        @click="
                            currentProduct.cart_quantity++;
                            addItem(currentProduct);
                        "
                        type="primary"
                        size="large"
                    >
                        <ShoppingCartOutlined />
                        {{ $t("front_setting.add_to_cart") }}
                    </a-button>
                </div>
            </a-col>
        </a-row>
    </a-modal>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import { useStore } from "vuex";
import { ShoppingCartOutlined, MinusOutlined, PlusOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import { filter, forEach } from "lodash-es";
import cart from "../../../../common/composable/cart";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default {
    props: ["product", "currency"],
    components: {
        ShoppingCartOutlined,
        MinusOutlined,
        PlusOutlined,
    },
    setup(props) {
        const { formatAmountCurrency } = cart();
        const store = useStore();
        const visible = ref(false);
        const productQuantity = ref(1);
        const currentProduct = ref({});

        onMounted(() => {
            const cartItems = store.getters["front/storeCartItems"];
            let productQuantity = 0;

            forEach(cartItems, (iee) => {
                if (iee.xid == props.product.xid) {
                    productQuantity = iee.cart_quantity;
                }
            });

            currentProduct.value = { ...props.product, cart_quantity: productQuantity };
        });

        const showModal = () => {
            visible.value = true;
        };

        const addItem = (product) => {
            const cartItems = store.getters["front/storeCartItems"];
            const updatedCartItems = filter(
                cartItems,
                (cartItem) => cartItem.xid != product.xid
            );

            if (product.cart_quantity > 0) {
                updatedCartItems.push({
                    ...product,
                    cart_quantity: product.cart_quantity,
                });
            }

            store.commit("front/addCartItems", updatedCartItems);
            message.success(`Item updated in cart`);
        };

        watch(store.state.front, (newVal, oldVal) => {
            let productQuantity = 0;
            forEach(store.getters["front/storeCartItems"], (iee) => {
                if (iee.xid == currentProduct.value.xid) {
                    productQuantity = iee.cart_quantity;
                }
            });

            currentProduct.value = {
                ...currentProduct.value,
                cart_quantity: productQuantity,
            };
        });

        return {
            currentProduct,
            formatAmountCurrency,

            visible,
            productQuantity,

            showModal,
            addItem,
            getSalesPriceWithTax,
        };
    },
};
</script>

<style lang="less">
.product {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 4px;
    margin-top: 15px;
}

.product-top {
    display: flex;
    justify-content: center;
    width: 100%;

    a {
        text-align: "center";
    }

    img {
        height: 180px;
        max-width: 100%;
        width: 100%;
        -o-object-fit: cover;
        object-fit: cover;
    }
}

.product-bottom {
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 15px;

    .product-category {
        font-weight: 500;
        font-size: 12px;
        color: #9ca3af;
    }

    .product-title {
        font-weight: 400;
        font-size: 14px;
        height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 15px;

        .product-price {
            span {
                font-weight: 600;
                font-size: 18px;
            }

            del {
                font-weight: 400;
                color: #9ca3af;
                margin-left: 5px;
            }
        }
    }
}

.product-details-image {
    display: flex;
    justify-content: center;
    height: 300px;

    span {
        box-sizing: border-box;
        display: inline-block;
        overflow: hidden;
        width: initial;
        height: initial;
        background: none;
        opacity: 1;
        border: 0px;
        margin: 0px;
        padding: 0px;
        position: relative;
        max-width: 100%;
    }

    img {
        display: block;
        max-width: 100%;
        width: initial;
        height: initial;
        background: none;
        opacity: 1;
        border: 0px;
        margin: 0px;
        padding: 0px;
    }
}
</style>

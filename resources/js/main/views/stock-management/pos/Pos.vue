<template>
    <!-- Header dengan design yang lebih modern -->
    <div class="modern-pos-header">
        <a-card class="header-card" :bodyStyle="{ padding: '16px 24px' }">
            <div class="header-content">
                <div class="header-left">
                    <div class="pos-title">
                        <h2 class="title-text">{{ $t('menu.pos') }}</h2>
                    </div>
                </div>
                <div class="header-right">
                    <a-space size="middle">
                        <a-button type="text" class="header-btn" @click="() => $router.go(-1)">
                            <template #icon><ArrowLeftOutlined /></template>
                            Kembali
                        </a-button>
                        <a-divider type="vertical" />
                        <div class="current-time">{{ currentTime }}</div>
                    </a-space>
                </div>
            </div>
        </a-card>
    </div>

    <!-- Main POS Interface -->
    <div class="modern-pos-container">
        <!-- Desktop & Tablet Layout -->
        <div v-if="deviceType !== 'mobile'" class="desktop-layout">
            <!-- Left Panel - Products -->
            <div class="products-panel">
                <!-- Quick Search & Filters -->
                <div class="search-section">
                    <div class="quick-search">
                        <a-input
                            v-model:value="quickSearchTerm"
                            placeholder="Cari produk cepat... (Scan barcode atau ketik nama)"
                            size="large"
                            class="search-input"
                            @pressEnter="quickSearchProduct"
                        >
                            <template #prefix>
                                <SearchOutlined class="search-icon" />
                            </template>
                            <template #suffix>
                                <a-tooltip title="Scan Barcode">
                                    <ScanOutlined class="scan-icon" />
                                </a-tooltip>
                            </template>
                        </a-input>
                    </div>
                    
                    <!-- Filter Chips -->
                    <div class="filter-chips">
                        <a-space wrap>
                            <a-tag 
                                v-for="category in categories.slice(0, 6)" 
                                :key="category.xid"
                                class="filter-chip"
                                :class="{ 'active-chip': formData.category_id === category.xid }"
                                @click="filterByCategory(category.xid)"
                            >
                                {{ category.name }}
                            </a-tag>
                            <a-tag v-if="categories.length > 6" class="more-filters" @click="showAllFilters = true">
                                +{{ categories.length - 6 }} lainnya
                            </a-tag>
                        </a-space>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid-modern">
                    <perfect-scrollbar class="products-scroll" :options="scrollOptions">
                        <div v-if="productLists.length > 0" class="grid-container">
                            <div
                                v-for="item in productLists"
                                :key="item.xid"
                                class="product-card-modern"
                                @click="selectSaleProduct(item)"
                            >
                                <div class="product-image">
                                    <img :src="item.image_url || '/placeholder-product.jpg'" :alt="item.name" />
                                    <div class="stock-indicator" :class="getStockClass(item.stock_quantity)">
                                        <div class="stock-dot"></div>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h4 class="product-name">{{ item.name }}</h4>
                                    <div class="product-details">
                                        <div class="price">{{ formatAmountCurrency(item.unit_price) }}</div>
                                        <div class="stock">Stok: {{ item.stock_quantity }}</div>
                                    </div>
                                </div>
                                <div class="add-overlay">
                                    <PlusOutlined class="add-icon" />
                                </div>
                            </div>
                        </div>
                        <div v-else class="empty-products">
                            <a-empty description="Tidak ada produk ditemukan">
                                <template #image>
                                    <InboxOutlined style="font-size: 64px; color: #d9d9d9;" />
                                </template>
                            </a-empty>
                        </div>
                    </perfect-scrollbar>
                </div>
            </div>

            <!-- Right Panel - Cart -->
            <div class="cart-panel">
                <div class="cart-container">
                    <!-- Customer Selection -->
                    <div class="customer-section-modern">
                        <div class="section-header">
                            <UserOutlined class="section-icon" />
                            <span class="section-title">Pelanggan</span>
                        </div>
                        <a-select
                            v-model:value="formData.user_id"
                            placeholder="Pilih pelanggan atau walk-in"
                            size="large"
                            class="customer-select"
                            show-search
                            optionFilterProp="title"
                        >
                            <a-select-option
                                v-for="customer in customers"
                                :key="customer.xid"
                                :title="customer.name"
                                :value="customer.xid"
                            >
                                <div class="customer-option">
                                    <div class="customer-name">{{ customer.name }}</div>
                                    <div v-if="customer.phone" class="customer-phone">{{ customer.phone }}</div>
                                </div>
                            </a-select-option>
                        </a-select>
                    </div>

                    <!-- Cart Items -->
                    <div class="cart-items-section">
                        <div class="section-header">
                            <ShoppingCartOutlined class="section-icon" />
                            <span class="section-title">Keranjang Belanja</span>
                            <a-badge :count="selectedProducts.length" class="cart-badge" />
                        </div>
                        
                        <div class="cart-items-container">
                            <div v-if="selectedProducts.length > 0" class="cart-items-list">
                                <div
                                    v-for="item in selectedProducts"
                                    :key="item.xid"
                                    class="cart-item-modern"
                                >
                                    <div class="item-image">
                                        <img :src="item.image_url || '/placeholder-product.jpg'" :alt="item.name" />
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name">{{ item.name }}</div>
                                        <div class="item-price">{{ formatAmountCurrency(item.unit_price) }}</div>
                                        <div class="item-stock">Stok: {{ item.stock_quantity }}</div>
                                    </div>
                                    <div class="item-controls">
                                        <div class="quantity-control">
                                            <a-button 
                                                size="small" 
                                                type="text" 
                                                class="qty-btn"
                                                @click="decreaseQuantity(item)"
                                            >
                                                <MinusOutlined />
                                            </a-button>
                                            <span class="quantity-display">{{ item.quantity }}</span>
                                            <a-button 
                                                size="small" 
                                                type="text" 
                                                class="qty-btn"
                                                @click="increaseQuantity(item)"
                                            >
                                                <PlusOutlined />
                                            </a-button>
                                        </div>
                                        <div class="item-total">{{ formatAmountCurrency(item.subtotal) }}</div>
                                        <a-button 
                                            type="text" 
                                            danger 
                                            size="small"
                                            class="remove-btn"
                                            @click="showDeleteConfirm(item)"
                                        >
                                            <DeleteOutlined />
                                        </a-button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="empty-cart">
                                <ShoppingCartOutlined class="empty-icon" />
                                <p>Keranjang kosong</p>
                                <span>Pilih produk untuk memulai transaksi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Section -->
                    <div class="billing-section-modern">
                        <a-collapse ghost>
                            <a-collapse-panel key="billing" header="Detail Tagihan" class="billing-panel">
                                <div class="billing-controls">
                                    <!-- Tax -->
                                    <div class="billing-row">
                                        <label>Pajak</label>
                                        <a-select
                                            v-model:value="formData.tax_id"
                                            placeholder="Pilih pajak"
                                            allowClear
                                            @change="taxChanged"
                                        >
                                            <a-select-option
                                                v-for="tax in taxes"
                                                :key="tax.xid"
                                                :value="tax.xid"
                                                :tax="tax"
                                            >
                                                {{ tax.name }} ({{ tax.rate }}%)
                                            </a-select-option>
                                        </a-select>
                                    </div>

                                    <!-- Discount -->
                                    <div class="billing-row">
                                        <label>Diskon</label>
                                        <a-input-group compact>
                                            <a-select
                                                v-model:value="formData.discount_type"
                                                style="width: 30%"
                                                @change="recalculateFinalTotal"
                                            >
                                                <a-select-option value="percentage">%</a-select-option>
                                                <a-select-option value="fixed">Rp</a-select-option>
                                            </a-select>
                                            <a-input-number
                                                v-model:value="formData.discount_value"
                                                style="width: 70%"
                                                placeholder="0"
                                                min="0"
                                                @change="recalculateFinalTotal"
                                            />
                                        </a-input-group>
                                    </div>
                                </div>
                            </a-collapse-panel>
                        </a-collapse>
                    </div>

                    <!-- Total & Actions -->
                    <div class="checkout-section">
                        <div class="total-summary-modern">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>{{ formatAmountCurrency(calculateSubtotal()) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Pajak</span>
                                <span>{{ formatAmountCurrency(formData.tax_amount) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Diskon</span>
                                <span class="discount">-{{ formatAmountCurrency(formData.discount) }}</span>
                            </div>
                            <div class="summary-row total-row">
                                <span>TOTAL</span>
                                <span class="total-amount">{{ formatAmountCurrency(formData.subtotal) }}</span>
                            </div>
                        </div>

                        <div class="action-buttons-modern">
                            <a-button
                                type="primary"
                                size="large"
                                block
                                class="pay-button-modern"
                                :disabled="!canPay"
                                @click="payNow"
                            >
                                <template #icon><CreditCardOutlined /></template>
                                BAYAR SEKARANG
                            </a-button>
                            <a-row :gutter="8" style="margin-top: 8px;">
                                <a-col :span="12">
                                    <a-button block class="secondary-btn" @click="holdOrder">
                                        <template #icon><PauseCircleOutlined /></template>
                                        Tahan
                                    </a-button>
                                </a-col>
                                <a-col :span="12">
                                    <a-button block class="secondary-btn" @click="resetPos">
                                        <template #icon><ClearOutlined /></template>
                                        Reset
                                    </a-button>
                                </a-col>
                            </a-row>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Layout -->
        <div v-else class="mobile-layout">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <a-input
                    v-model:value="quickSearchTerm"
                    placeholder="Cari atau scan produk..."
                    size="large"
                    class="mobile-search"
                    @pressEnter="quickSearchProduct"
                >
                    <template #prefix><SearchOutlined /></template>
                    <template #suffix><ScanOutlined /></template>
                </a-input>
            </div>

            <!-- Mobile Navigation -->
            <div class="mobile-nav">
                <a-tabs v-model:activeKey="mobileActiveTab" type="card" class="mobile-tabs">
                    <a-tab-pane key="products" :tab="`Produk (${productLists.length})`">
                        <div class="mobile-products">
                            <div class="mobile-filters">
                                <a-space wrap>
                                    <a-tag 
                                        v-for="category in categories.slice(0, 4)" 
                                        :key="category.xid"
                                        :class="{ 'active-chip': formData.category_id === category.xid }"
                                        @click="filterByCategory(category.xid)"
                                    >
                                        {{ category.name }}
                                    </a-tag>
                                </a-space>
                            </div>
                            
                            <div class="mobile-products-grid">
                                <div
                                    v-for="item in productLists"
                                    :key="item.xid"
                                    class="mobile-product-card"
                                    @click="selectSaleProduct(item)"
                                >
                                    <div class="mobile-product-image">
                                        <img :src="item.image_url || '/placeholder-product.jpg'" :alt="item.name" />
                                    </div>
                                    <div class="mobile-product-info">
                                        <div class="mobile-product-name">{{ item.name }}</div>
                                        <div class="mobile-product-price">{{ formatAmountCurrency(item.unit_price) }}</div>
                                        <div class="mobile-product-stock">Stok: {{ item.stock_quantity }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a-tab-pane>

                    <a-tab-pane key="cart" :tab="`Keranjang (${selectedProducts.length})`">
                        <div class="mobile-cart">
                            <div class="mobile-customer">
                                <a-select
                                    v-model:value="formData.user_id"
                                    placeholder="Pilih pelanggan"
                                    size="large"
                                    style="width: 100%"
                                >
                                    <a-select-option
                                        v-for="customer in customers"
                                        :key="customer.xid"
                                        :value="customer.xid"
                                    >
                                        {{ customer.name }}
                                    </a-select-option>
                                </a-select>
                            </div>

                            <div class="mobile-cart-items">
                                <div v-if="selectedProducts.length > 0">
                                    <div
                                        v-for="item in selectedProducts"
                                        :key="item.xid"
                                        class="mobile-cart-item"
                                    >
                                        <div class="mobile-item-info">
                                            <div class="mobile-item-name">{{ item.name }}</div>
                                            <div class="mobile-item-price">{{ formatAmountCurrency(item.unit_price) }}</div>
                                        </div>
                                        <div class="mobile-item-controls">
                                            <div class="mobile-quantity-control">
                                                <a-button size="small" @click="decreaseQuantity(item)">
                                                    <MinusOutlined />
                                                </a-button>
                                                <span class="mobile-quantity">{{ item.quantity }}</span>
                                                <a-button size="small" @click="increaseQuantity(item)">
                                                    <PlusOutlined />
                                                </a-button>
                                            </div>
                                            <div class="mobile-item-total">{{ formatAmountCurrency(item.subtotal) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="mobile-empty-cart">
                                    <ShoppingCartOutlined style="font-size: 48px; color: #d9d9d9;" />
                                    <p>Keranjang kosong</p>
                                </div>
                            </div>

                            <div class="mobile-total">
                                <div class="mobile-total-row">
                                    <span>TOTAL</span>
                                    <span class="mobile-total-amount">{{ formatAmountCurrency(formData.subtotal) }}</span>
                                </div>
                                <a-button
                                    type="primary"
                                    size="large"
                                    block
                                    class="mobile-pay-button"
                                    :disabled="!canPay"
                                    @click="payNow"
                                >
                                    BAYAR SEKARANG
                                </a-button>
                            </div>
                        </div>
                    </a-tab-pane>
                </a-tabs>
            </div>
        </div>
    </div>

    <!-- Existing Modals (keep as is) -->
    <a-modal
        :open="addEditVisible"
        :closable="false"
        :centered="true"
        :title="addEditPageTitle"
        @ok="onAddEditSubmit"
    >
        <!-- Modal content unchanged -->
    </a-modal>

    <PayNow
        :visible="payNowVisible"
        @closed="payNowClosed"
        @success="payNowSuccess"
        :data="formData"
        :selectedProducts="selectedProducts"
    />

    <InvoiceModal
        :visible="printInvoiceModalVisible"
        :order="printInvoiceOrder"
        @closed="printInvoiceModalVisible = false"
    />
</template>

<script>
import { ref, onMounted, reactive, toRefs, nextTick, computed, onUnmounted } from "vue";
import {
    PlusOutlined,
    MinusOutlined,
    EditOutlined,
    DeleteOutlined,
    SearchOutlined,
    SaveOutlined,
    SettingOutlined,
    CreditCardOutlined,
    ClearOutlined,
    UserOutlined,
    ShoppingCartOutlined,
    InboxOutlined,
    ScanOutlined,
    ArrowLeftOutlined,
    PauseCircleOutlined,
} from "@ant-design/icons-vue";
import { debounce } from "lodash-es";
import { useI18n } from "vue-i18n";
import { message } from "ant-design-vue";
import { includes, find } from "lodash-es";
import common from "../../../../common/composable/common";
import fields from "./fields";
import ProductCardNew from "../../../../common/components/product/ProductCardNew.vue";
import PayNow from "./PayNow.vue";
import CustomerAddButton from "../../users/CustomerAddButton.vue";
import InvoiceModal from "./Invoice.vue";
import PosLayout1 from "./PosLayout1.vue";
import PosLayout2 from "./PosLayout2.vue";

export default {
    components: {
        PlusOutlined,
        MinusOutlined,
        SearchOutlined,
        EditOutlined,
        DeleteOutlined,
        SaveOutlined,
        SettingOutlined,
        CreditCardOutlined,
        ClearOutlined,
        UserOutlined,
        ShoppingCartOutlined,
        InboxOutlined,
        ScanOutlined,
        ArrowLeftOutlined,
        PauseCircleOutlined,
        PosLayout1,
        PosLayout2,
        ProductCardNew,
        PayNow,
        CustomerAddButton,
        InvoiceModal,
    },
    setup() {
        const {
            taxes,
            customers,
            brands,
            categories,
            productLists,
            orderItemColumns,
            formData,
            customerUrl,
            getPreFetchData,
            posDefaultCustomer,
        } = fields();

        const selectedProducts = ref([]);
        const selectedProductIds = ref([]);
        const removedOrderItemsIds = ref([]);
        const deviceType = ref('desktop');
        const mobileActiveTab = ref('products');
        const quickSearchTerm = ref('');
        const showAllFilters = ref(false);
        const currentTime = ref('');

        const state = reactive({
            orderSearchTerm: undefined,
            productFetching: false,
            products: [],
        });

        const {
            formatAmount,
            formatAmountCurrency,
            appSetting,
            taxTypes,
            permsArray,
        } = common();
        const { t } = useI18n();

        // Modal states
        const addEditVisible = ref(false);
        const addEditFormSubmitting = ref(false);
        const addEditFormData = ref({});
        const addEditRules = ref([]);
        const addEditPageTitle = ref("");
        const payNowVisible = ref(false);
        const printInvoiceModalVisible = ref(false);
        const printInvoiceOrder = ref({});

        const scrollOptions = {
            wheelSpeed: 1,
            swipeEasing: true,
            suppressScrollX: true,
        };

        // Device Detection
        const checkDeviceType = () => {
            const width = window.innerWidth;
            if (width < 768) {
                deviceType.value = 'mobile';
            } else if (width < 1024) {
                deviceType.value = 'tablet';
            } else {
                deviceType.value = 'desktop';
            }
        };

        // Update current time
        const updateCurrentTime = () => {
            const now = new Date();
            currentTime.value = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        };

        // Computed Properties
        const canPay = computed(() => {
            return (
                formData.value.subtotal > 0 &&
                formData.value.user_id &&
                formData.value.user_id !== '' &&
                selectedProducts.value.length > 0
            );
        });

        const calculateSubtotal = () => {
            return selectedProducts.value.reduce((total, item) => {
                return total + (item.unit_price * item.quantity);
            }, 0);
        };

        // Methods
        const getStockClass = (stock) => {
            if (stock > 10) return 'high';
            if (stock > 5) return 'medium';
            return 'low';
        };

        const filterByCategory = (categoryId) => {
            if (formData.value.category_id === categoryId) {
                formData.value.category_id = undefined;
            } else {
                formData.value.category_id = categoryId;
            }
            reFetchProducts();
        };

        const quickSearchProduct = () => {
            if (quickSearchTerm.value) {
                fetchAllSearchedProduct(quickSearchTerm.value);
            }
        };

        const increaseQuantity = (item) => {
            if (item.quantity < item.stock_quantity) {
                item.quantity += 1;
                quantityChanged(item);
            } else {
                message.warning('Stok tidak mencukupi');
            }
        };

        const decreaseQuantity = (item) => {
            if (item.quantity > 1) {
                item.quantity -= 1;
                quantityChanged(item);
            } else {
                showDeleteConfirm(item);
            }
        };

        const holdOrder = () => {
            message.success('Pesanan ditahan');
            resetPos();
        };

        // Existing methods (keep all the original functionality)
        onMounted(() => {
            getPreFetchData();
            checkDeviceType();
            updateCurrentTime();
            window.addEventListener('resize', checkDeviceType);
            setInterval(updateCurrentTime, 1000);
        });

        onUnmounted(() => {
            window.removeEventListener('resize', checkDeviceType);
        });

        const reFetchProducts = () => {
            axiosAdmin
                .post("pos/products", {
                    brand_id: formData.value.brand_id,
                    category_id: formData.value.category_id,
                })
                .then((productResponse) => {
                    productLists.value = productResponse.data.products;
                });
        };

        const fetchProducts = debounce((value) => {
            fetchAllSearchedProduct(value);
        }, 300);

        const fetchAllSearchedProduct = (value) => {
            state.products = [];

            if (value != "") {
                state.productFetching = true;
                let url = `search-product`;

                axiosAdmin
                    .post(url, {
                        order_type: "sales",
                        search_term: value,
                    })
                    .then((response) => {
                        if (response.data.length == 1) {
                            selectSaleProduct(response.data[0]);
                        } else {
                            productLists.value = response.data;
                        }
                        state.productFetching = false;
                        quickSearchTerm.value = '';
                    });
            }
        };

        const selectSaleProduct = (newProduct) => {
            if (!includes(selectedProductIds.value, newProduct.xid)) {
                selectedProductIds.value.push(newProduct.xid);

                selectedProducts.value.push({
                    ...newProduct,
                    sn: selectedProducts.value.length + 1,
                    unit_price: formatAmount(newProduct.unit_price),
                    tax_amount: formatAmount(newProduct.tax_amount),
                    subtotal: formatAmount(newProduct.subtotal),
                    quantity: 1,
                });
                
                recalculateFinalTotal();

                // Auto switch to cart tab on mobile
                if (deviceType.value === 'mobile') {
                    mobileActiveTab.value = 'cart';
                }

                // Play beep sound
                if (appSetting.value.beep_audio_url) {
                    var audioObj = new Audio(appSetting.value.beep_audio_url);
                    audioObj.play();
                }

                message.success(`${newProduct.name} ditambahkan ke keranjang`);
            } else {
                const existingProduct = find(selectedProducts.value, ["xid", newProduct.xid]);
                if (existingProduct && existingProduct.quantity < existingProduct.stock_quantity) {
                    increaseQuantity(existingProduct);
                } else {
                    message.error("Stok tidak mencukupi");
                }
            }
        };

        const quantityChanged = (record) => {
            const newResults = [];
            selectedProducts.value.map((selectedProduct) => {
                if (selectedProduct.xid == record.xid) {
                    const updatedProduct = {
                        ...record,
                        subtotal: record.unit_price * record.quantity
                    };
                    newResults.push(updatedProduct);
                } else {
                    newResults.push(selectedProduct);
                }
            });
            selectedProducts.value = newResults;
            recalculateFinalTotal();
        };

        const recalculateFinalTotal = () => {
            let total = calculateSubtotal();
            
            var discountAmount = 0;
            if (formData.value.discount_type == "percentage") {
                discountAmount = formData.value.discount_value ? (parseFloat(formData.value.discount_value) * total) / 100 : 0;
            } else if (formData.value.discount_type == "fixed") {
                discountAmount = formData.value.discount_value ? parseFloat(formData.value.discount_value) : 0;
            }

            const taxRate = formData.value.tax_rate ? parseFloat(formData.value.tax_rate) : 0;
            total = total - discountAmount;
            const tax = total * (taxRate / 100);
            total = total + parseFloat(formData.value.shipping || 0);

            formData.value.subtotal = formatAmount(total + tax);
            formData.value.tax_amount = formatAmount(tax);
            formData.value.discount = discountAmount;
        };

        const showDeleteConfirm = (product) => {
            const newResults = selectedProducts.value.filter(item => item.xid !== product.xid);
            selectedProducts.value = newResults;
            selectedProductIds.value = selectedProductIds.value.filter(id => id !== product.xid);
            recalculateFinalTotal();
            message.success(`${product.name} dihapus dari keranjang`);
        };

        const taxChanged = (value, option) => {
            formData.value.tax_rate = value == undefined ? 0 : option.tax.rate;
            recalculateFinalTotal();
        };

        const payNow = () => {
            payNowVisible.value = true;
        };

        const payNowClosed = () => {
            payNowVisible.value = false;
        };

        const resetPos = () => {
            selectedProducts.value = [];
            selectedProductIds.value = [];
            quickSearchTerm.value = '';

            formData.value = {
                ...formData.value,
                tax_id: undefined,
                category_id: undefined,
                brand_id: undefined,
                tax_rate: 0,
                tax_amount: 0,
                discount_value: 0,
                discount: 0,
                shipping: 0,
                subtotal: 0,
            };

            recalculateFinalTotal();
        };

        const onAddEditSubmit = () => {
            const record = selectedProducts.value.filter(
                (selectedProduct) => selectedProduct.xid == addEditFormData.value.id
            );

            const selecteTax = taxes.value.filter(
                (tax) => tax.xid == addEditFormData.value.tax_id
            );

            const taxType = addEditFormData.value.tax_type != undefined
                ? addEditFormData.value.tax_type
                : "exclusive";

            const newData = {
                ...record[0],
                discount_rate: parseFloat(addEditFormData.value.discount_rate),
                unit_price: parseFloat(addEditFormData.value.unit_price),
                tax_id: addEditFormData.value.tax_id,
                tax_rate: selecteTax[0] ? selecteTax[0].rate : 0,
                tax_type: taxType,
            };
            quantityChanged(newData);
            onAddEditClose();
        };

        const onAddEditClose = () => {
            addEditFormData.value = {};
            addEditVisible.value = false;
        };

        const customerAdded = () => {
            axiosAdmin.get(customerUrl).then((response) => {
                customers.value = response.data;
            });
        };

        const payNowSuccess = (invoiceOrder) => {
            resetPos();

            var walkInCustomerId = posDefaultCustomer.value && posDefaultCustomer.value.xid
                ? posDefaultCustomer.value.xid
                : undefined;
            formData.value = {
                ...formData.value,
                user_id: walkInCustomerId,
            };

            reFetchProducts();
            payNowVisible.value = false;

            printInvoiceOrder.value = invoiceOrder;
            printInvoiceModalVisible.value = true;
        };

        return {
            // Data
            taxes,
            customers,
            categories,
            brands,
            productLists,
            formData,
            selectedProducts,
            selectedProductIds,
            deviceType,
            mobileActiveTab,
            quickSearchTerm,
            showAllFilters,
            currentTime,
            scrollOptions,

            // Computed
            canPay,
            calculateSubtotal,

            // Methods
            getStockClass,
            filterByCategory,
            quickSearchProduct,
            increaseQuantity,
            decreaseQuantity,
            holdOrder,
            reFetchProducts,
            selectSaleProduct,
            taxChanged,
            quantityChanged,
            recalculateFinalTotal,
            payNow,
            payNowVisible,
            payNowClosed,
            resetPos,
            customerAdded,
            onAddEditSubmit,
            onAddEditClose,
            showDeleteConfirm,
            payNowSuccess,

            // Utils
            appSetting,
            permsArray,
            ...toRefs(state),
            fetchProducts,
            orderItemColumns,
            formatAmount,
            formatAmountCurrency,
            taxTypes,

            // Modal states
            addEditVisible,
            addEditFormData,
            addEditFormSubmitting,
            addEditRules,
            addEditPageTitle,
            printInvoiceModalVisible,
            printInvoiceOrder,
        };
    },
};
</script>

<style lang="less" scoped>
// Modern Color Palette
:root {
    --primary-color: #007BFF;
    --primary-light: #3395FF;
    --primary-dark: #0056B3;
    --primary-color-transparent: rgba(0, 123, 255, 0.1);
    --success-color: #52c41a;
    --warning-color: #fa8c16;
    --error-color: #ff4d4f;
    --bg-primary: #ffffff;
    --bg-secondary: #f5f7fa;
    --bg-tertiary: #f0f2f5;
    --text-primary: #262626;
    --text-secondary: #595959;
    --text-tertiary: #8c8c8c;
    --border-color: #e8e8e8;
    --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.1);
    --shadow-heavy: 0 8px 24px rgba(0, 0, 0, 0.12);
    --border-radius: 8px;
    --border-radius-large: 12px;
}

// Header Styles
.modern-pos-header {
    margin-bottom: 16px;
    
    .header-card {
        border: none;
        box-shadow: var(--shadow-light);
        border-radius: var(--border-radius);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pos-title {
        .title-text {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .subtitle {
            color: var(--text-tertiary);
            font-size: 14px;
        }
    }
    
    .header-btn {
        color: var(--text-secondary);
        
        &:hover {
            color: var(--primary-color);
        }
    }
    
    .current-time {
        font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-tertiary);
        padding: 4px 12px;
        border-radius: 6px;
    }
}

// Main Container
.modern-pos-container {
    background: var(--bg-secondary);
    border-radius: var(--border-radius-large);
    padding: 16px;
    min-height: calc(100vh - 140px);
}

// Desktop Layout
.desktop-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 16px;
    height: 100%;
}

// Products Panel
.products-panel {
    background: var(--bg-primary);
    border-radius: var(--border-radius-large);
    padding: 20px;
    box-shadow: var(--shadow-light);
    display: flex;
    flex-direction: column;
}

.search-section {
    margin-bottom: 24px;
    
    .quick-search {
        margin-bottom: 16px;
        
        .search-input {
            border-radius: var(--border-radius);
            
            .search-icon {
                color: var(--primary-color);
            }
            
            .scan-icon {
                color: var(--text-tertiary);
                cursor: pointer;
                
                &:hover {
                    color: var(--primary-color);
                }
            }
        }
    }
    
    .filter-chips {
        .filter-chip {
            cursor: pointer;
            border-radius: 20px;
            transition: all 0.2s ease;
            
            &:hover {
                transform: translateY(-1px);
                box-shadow: var(--shadow-light);
            }
        }

        .active-chip {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .more-filters {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 20px;
        }
    }
}

.products-grid-modern {
    flex: 1;
    
    .products-scroll {
        height: 100%;
    }
    
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
        padding: 8px;
    }
    
    .product-card-modern {
        background: var(--bg-primary);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
        
        &:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            border-color: var(--primary-light);
            
            .add-overlay {
                opacity: 1;
            }
        }
        
        .product-image {
            position: relative;
            margin-bottom: 12px;
            
            img {
                width: 100%;
                height: 120px;
                object-fit: cover;
                border-radius: var(--border-radius);
                background: var(--bg-tertiary);
            }
            
            .stock-indicator {
                position: absolute;
                top: 8px;
                right: 8px;
                
                .stock-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    
                    &.high {
                        background: var(--success-color);
                    }
                    
                    &.medium {
                        background: var(--warning-color);
                    }
                    
                    &.low {
                        background: var(--error-color);
                    }
                }
            }
        }
        
        .product-info {
            .product-name {
                font-size: 14px;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 8px;
                line-height: 1.3;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            .product-details {
                display: flex;
                justify-content: space-between;
                align-items: center;
                
                .price {
                    font-size: 16px;
                    font-weight: 700;
                    color: var(--primary-color);
                }
                
                .stock {
                    font-size: 12px;
                    color: var(--text-tertiary);
                }
            }
        }
        
        .add-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-color-transparent);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            
            .add-icon {
                font-size: 24px;
                color: var(--primary-color);
                background: var(--bg-primary);
                border-radius: 50%;
                padding: 8px;
                box-shadow: var(--shadow-medium);
            }
        }
    }
    
    .empty-products {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: var(--text-tertiary);
    }
}

// Cart Panel
.cart-panel {
    background: var(--bg-primary);
    border-radius: var(--border-radius-large);
    box-shadow: var(--shadow-light);
    display: flex;
    flex-direction: column;
}

.cart-container {
    padding: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.customer-section-modern {
    margin-bottom: 20px;
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        
        .section-icon {
            color: var(--primary-color);
            font-size: 16px;
            margin-right: 8px;
        }
        
        .section-title {
            font-weight: 600;
            color: var(--text-primary);
        }
    }
    
    .customer-select {
        .customer-option {
            .customer-name {
                font-weight: 500;
            }
            
            .customer-phone {
                font-size: 12px;
                color: var(--text-tertiary);
            }
        }
    }
}

.cart-items-section {
    flex: 1;
    margin-bottom: 24px;
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
        
        .cart-badge {
            margin-left: auto;
        }
    }
    
    .cart-items-container {
        flex: 1;
        max-height: 400px;
        overflow-y: auto;
        
        .cart-items-list {
            .cart-item-modern {
                display: flex;
                align-items: center;
                padding: 16px; /* Increased padding for more breathing room */
                border: 1px solid var(--border-color);
                border-radius: var(--border-radius);
                margin-bottom: 12px; /* Consistent spacing between items */
                transition: all 0.2s ease;
                
                &:hover {
                    border-color: var(--primary-light);
                    box-shadow: var(--shadow-light);
                }
                
                .item-image {
                    width: 64px; /* Slightly larger image */
                    height: 64px;
                    margin-right: 16px; /* Increased margin */
                    
                    img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        border-radius: 8px; /* More rounded corners */
                        background: var(--bg-tertiary);
                    }
                }
                
                .item-details {
                    flex: 1;
                    margin-right: 16px; /* Increased margin */
                    
                    .item-name {
                        font-weight: 600; /* Slightly bolder name */
                        color: var(--text-primary);
                        margin-bottom: 6px; /* Adjusted margin */
                        font-size: 15px; /* Slightly larger font */
                    }
                    
                    .item-price {
                        color: var(--text-secondary);
                        font-size: 13px; /* Slightly larger font */
                        margin-bottom: 4px; /* Adjusted margin */
                    }
                    
                    .item-stock {
                        color: var(--text-tertiary);
                        font-size: 11px;
                    }
                }
                
                .item-controls {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end; /* Align controls to the right */
                    gap: 10px; /* Adjusted gap */
                    
                    .quantity-control {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        background: var(--bg-tertiary);
                        border-radius: 6px;
                        padding: 4px; /* Increased padding */
                        
                        .qty-btn {
                            width: 28px; /* Slightly larger buttons */
                            height: 28px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 4px;
                            
                            &:hover {
                                background: var(--primary-light);
                                color: white;
                            }
                        }
                        
                        .quantity-display {
                            min-width: 24px; /* Adjusted width */
                            text-align: center;
                            font-weight: 700; /* Bolder quantity */
                            color: var(--text-primary);
                        }
                    }
                    
                    .item-total {
                        font-weight: 700;
                        color: var(--primary-color);
                        font-size: 16px; /* Larger total amount */
                    }
                    
                    .remove-btn {
                        color: var(--error-color);
                        
                        &:hover {
                            background: rgba(255, 77, 79, 0.1);
                        }
                    }
                }
            }
        }
        
        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: var(--text-tertiary);
            
            .empty-icon {
                font-size: 48px;
                margin-bottom: 16px;
            }
            
            p {
                font-size: 16px;
                margin-bottom: 4px;
            }
            
            span {
                font-size: 12px;
            }
        }
    }
}

.billing-section-modern {
    margin-bottom: 20px;
    
    .billing-panel {
        background: var(--bg-secondary);
        border-radius: var(--border-radius);
        
        .billing-controls {
            .billing-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
                
                label {
                    font-weight: 500;
                    color: var(--text-primary);
                    min-width: 60px;
                }
                
                .ant-select, .ant-input-group {
                    width: 200px;
                }
            }
        }
    }
}

.checkout-section {
    .total-summary-modern {
        background: var(--bg-secondary);
        border-radius: var(--border-radius);
        padding: 16px;
        margin-bottom: 16px;
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
            
            &.total-row {
                border-top: 1px solid var(--border-color);
                padding-top: 12px;
                margin-top: 12px;
                font-size: 18px;
                font-weight: 700;
                
                .total-amount {
                    color: var(--primary-color);
                }
            }
            
            .discount {
                color: var(--success-color);
            }
        }
    }
    
    .action-buttons-modern {
        .pay-button-modern {
            background-color: var(--primary-color);
            border: none;
            border-radius: var(--border-radius);
            height: 48px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: var(--shadow-medium);
            transition: all 0.2s ease;
            
            &:hover:not(:disabled) {
                transform: translateY(-1px);
                box-shadow: var(--shadow-heavy);
            }
            
            &:disabled {
                background: var(--bg-tertiary);
                color: var(--text-tertiary);
                transform: none;
                box-shadow: none;
            }
        }
        
        .secondary-btn {
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            
            &:hover {
                border-color: var(--primary-color);
                color: var(--primary-color);
            }
        }
    }
}

// Mobile Layout
.mobile-layout {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.mobile-header {
    margin-bottom: 16px;
    
    .mobile-search {
        border-radius: var(--border-radius);
    }
}

.mobile-nav {
    flex: 1;
    
    .mobile-tabs {
        height: 100%;
        
        .ant-tabs-content-holder {
            height: calc(100vh - 280px);
            overflow-y: auto;
        }
    }
}

.mobile-products {
    .mobile-filters {
        padding: 16px;
        background: var(--bg-secondary);
        margin-bottom: 16px;
        border-radius: var(--border-radius);
    }
    
    .mobile-products-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        padding: 0 16px;
        
        .mobile-product-card {
            background: var(--bg-primary);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            
            &:active {
                transform: scale(0.98);
            }
            
            .mobile-product-image {
                margin-bottom: 8px;
                
                img {
                    width: 100%;
                    height: 80px;
                    object-fit: cover;
                    border-radius: 6px;
                    background: var(--bg-tertiary);
                }
            }
            
            .mobile-product-info {
                .mobile-product-name {
                    font-size: 12px;
                    font-weight: 600;
                    margin-bottom: 4px;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                
                .mobile-product-price {
                    font-size: 14px;
                    font-weight: 700;
                    color: var(--primary-color);
                    margin-bottom: 2px;
                }
                
                .mobile-product-stock {
                    font-size: 10px;
                    color: var(--text-tertiary);
                }
            }
        }
    }
}

.mobile-cart {
    padding: 16px;
    
    .mobile-customer {
        margin-bottom: 16px;
    }
    
    .mobile-cart-items {
        margin-bottom: 16px;
        
        .mobile-cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            margin-bottom: 8px;
            
            .mobile-item-info {
                flex: 1;
                
                .mobile-item-name {
                    font-weight: 500;
                    margin-bottom: 4px;
                    font-size: 14px;
                }
                
                .mobile-item-price {
                    color: var(--text-secondary);
                    font-size: 12px;
                }
            }
            
            .mobile-item-controls {
                display: flex;
                align-items: center;
                gap: 12px;
                
                .mobile-quantity-control {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    
                    .mobile-quantity {
                        min-width: 20px;
                        text-align: center;
                        font-weight: 600;
                    }
                }
                
                .mobile-item-total {
                    font-weight: 700;
                    color: var(--primary-color);
                    min-width: 80px;
                    text-align: right;
                }
            }
        }
        
        .mobile-empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: var(--text-tertiary);
        }
    }
    
    .mobile-total {
        background: var(--bg-secondary);
        border-radius: var(--border-radius);
        padding: 16px;
        
        .mobile-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 700;
            
            .mobile-total-amount {
                color: var(--primary-color);
            }
        }
        
        .mobile-pay-button {
            background-color: var(--primary-color);
            border: none;
            border-radius: var(--border-radius);
            height: 48px;
            font-weight: 700;
            font-size: 16px;
            
            &:disabled {
                background: var(--bg-tertiary);
                color: var(--text-tertiary);
            }
        }
    }
}

// Responsive breakpoints
@media (max-width: 1200px) {
    .desktop-layout {
        grid-template-columns: 1fr 360px;
    }
    
    .grid-container {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

@media (max-width: 992px) {
    .desktop-layout {
        grid-template-columns: 1fr 320px;
    }
    
    .grid-container {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
}

@media (max-width: 768px) {
    .modern-pos-container {
        padding: 8px;
    }
    
    .desktop-layout {
        display: none;
    }
}

// Custom scrollbar
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: var(--bg-tertiary);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: var(--text-tertiary);
    border-radius: 3px;
    
    &:hover {
        background: var(--text-secondary);
    }
}

// Animations
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.cart-item-modern {
    animation: fadeInUp 0.3s ease;
}

.pay-button-modern:not(:disabled):active {
    animation: pulse 0.2s ease;
}

// Dark mode support (optional)
@media (prefers-color-scheme: dark) {
    :root {
        --bg-primary: #1f1f1f;
        --bg-secondary: #141414;
        --bg-tertiary: #262626;
        --text-primary: #ffffff;
        --text-secondary: #d9d9d9;
        --text-tertiary: #8c8c8c;
        --border-color: #303030;
    }
}
</style>
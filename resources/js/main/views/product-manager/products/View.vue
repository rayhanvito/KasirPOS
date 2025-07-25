<template>
    <a-drawer
        :title="product.name"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
        :destroyOnClose="true"
    >
        <div class="product-details">
            <a-row :gutter="[16, 16]">
                <a-col
                    v-if="product.product_type == 'single'"
                    :xs="24"
                    :sm="24"
                    :md="4"
                    :lg="4"
                >
                    <a-image :src="product.image_url" />
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="product.product_type == 'single' ? 20 : 24"
                    :lg="product.product_type == 'single' ? 20 : 24"
                >
                    <a-row
                        class="mb-10 mt-10"
                        v-if="product.details && product.details.status == 'out_of_stock'"
                    >
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-alert type="error">
                                <template #message>
                                    <span
                                        v-html="
                                            $t('messages.product_out_of_stock', [
                                                product.details.current_stock,
                                                product.details.stock_quantitiy_alert,
                                            ])
                                        "
                                    ></span>
                                </template>
                            </a-alert>
                        </a-col>
                    </a-row>

                    <a-row class="mb-10 mt-10">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-descriptions
                                :title="$t(`product.details`)"
                                layout="vertical"
                                :contentStyle="{ fontWeight: 500, marginBottom: '20px' }"
                            >
                                <a-descriptions-item :label="$t('product.name')">
                                    {{ product.name }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    v-if="product.product_type == 'single'"
                                    :label="$t('product.item_code')"
                                >
                                    {{ product.item_code }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('product.category')">
                                    {{
                                        product.category && product.category.xid
                                            ? product.category.name
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('product.brand')">
                                    {{
                                        product.brand && product.brand.xid
                                            ? product.brand.name
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <template v-if="product.product_type == 'single'">
                                    <a-descriptions-item
                                        :label="$t('product.current_stock')"
                                    >
                                        {{
                                            product.details &&
                                            product.details.current_stock
                                                ? `${product.details.current_stock} ${product.unit.short_name}`
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item
                                        :label="$t('product.quantitiy_alert')"
                                    >
                                        {{
                                            product.details &&
                                            product.details.stock_quantitiy_alert
                                                ? `${product.details.stock_quantitiy_alert} ${product.unit.short_name}`
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item
                                        :label="$t('product.sales_price')"
                                    >
                                        {{
                                            product.details && product.details.sales_price
                                                ? formatAmountCurrency(
                                                      product.details.sales_price
                                                  )
                                                : "-"
                                        }}
                                        {{
                                            product.details &&
                                            product.details.sales_tax_type == "inclusive"
                                                ? ` (${$t("common.with_tax")})`
                                                : ` (${$t("common.without_tax")})`
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item
                                        :label="$t('product.purchase_price')"
                                    >
                                        {{
                                            product.details &&
                                            product.details.purchase_price
                                                ? formatAmountCurrency(
                                                      product.details.purchase_price
                                                  )
                                                : "-"
                                        }}
                                        {{
                                            product.details &&
                                            product.details.purchase_tax_type ==
                                                "inclusive"
                                                ? ` (${$t("common.with_tax")})`
                                                : ` (${$t("common.without_tax")})`
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item :label="$t('product.mrp')">
                                        {{
                                            product.details && product.details.mrp
                                                ? formatAmountCurrency(
                                                      product.details.mrp
                                                  )
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item :label="$t('tax.rate')">
                                        {{
                                            product.details &&
                                            product.details.tax &&
                                            product.details.tax.rate
                                                ? `${product.details.tax.rate}%`
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                    <a-descriptions-item
                                        :label="$t('product.opening_stock')"
                                    >
                                        {{
                                            product.details &&
                                            product.details.opening_stock
                                                ? `${product.details.opening_stock} ${product.unit.short_name}`
                                                : "-"
                                        }}
                                    </a-descriptions-item>
                                </template>
                            </a-descriptions>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </div>

        <template v-if="product.product_type == 'variable'">
            <a-divider orientation="left">
                {{ $t("menu.variations") }}
            </a-divider>

            <a-table
                :dataSource="product.variations"
                :columns="variantTableColumns"
                :row-key="(record) => record.xid"
                :pagination="false"
                size="middle"
                class="mb-20"
            >
                <template #bodyCell="{ column, text, record }">
                    <template v-if="column.dataIndex === 'variant_id'">
                        {{ record.name }}
                    </template>
                    <template v-if="column.dataIndex === 'purchase_price'">
                        {{ formatAmountCurrency(record.details.purchase_price) }}
                    </template>

                    <template v-if="column.dataIndex === 'sales_price'">
                        {{ formatAmountCurrency(record.details.sales_price) }}
                    </template>
                    <template v-if="column.dataIndex === 'mrp'">
                        {{ formatAmountCurrency(record.details.mrp) }}
                    </template>
                </template>
            </a-table>
        </template>

        <ProductDetails v-else :product="product" />
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import ProductDetails from "./details/Details.vue";

export default defineComponent({
    props: ["product", "visible"],
    emits: ["closed"],
    components: {
        ProductDetails,
    },
    setup(props, { emit }) {
        const { formatAmountCurrency } = common();
        const { t } = useI18n();
        const variantTableColumns = ref([
            {
                title: t("variations.variation"),
                dataIndex: "variant_id",
            },
            {
                title: t("product.item_code"),
                dataIndex: "item_code",
            },
            {
                title: t("product.purchase_price"),
                dataIndex: "purchase_price",
            },
            {
                title: t("product.sales_price"),
                dataIndex: "sales_price",
            },
            {
                title: t("product.mrp"),
                dataIndex: "mrp",
            },
        ]);

        const onClose = () => {
            emit("closed");
        };

        return {
            formatAmountCurrency,
            onClose,
            variantTableColumns,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>

<style lang="less">
.product-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>

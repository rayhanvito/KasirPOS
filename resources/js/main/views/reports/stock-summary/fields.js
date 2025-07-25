import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const hashableColumns = ['category_id', 'brand_id'];
    const { formatAmountCurrency } = common();

    const columns = [
        {
            title: t("product.product"),
            dataIndex: "name",
            dbKey: "name",
        },
        {
            title: t("product.item_code"),
            dataIndex: "item_code",
            dbKey: "item_code",
        },
        {
            title: t("product.category"),
            dataIndex: "category_id",
            dbKey: "category.name",
        },
        {
            title: t("product.brand"),
            dataIndex: "brand_id",
            dbKey: "brand.name",
        },
        {
            title: t("product.purchase_price"),
            dataIndex: "purchase_price",
            dbKey: "details.purchase_price",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.purchase_price);
            }
        },
        {
            title: t("product.sales_price"),
            dataIndex: "sales_price",
            dbKey: "details.sales_price",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.sales_price);
            }
        },
        {
            title: t("product.current_stock"),
            dataIndex: "current_stock",
            dbKey: "details.current_stock",
            dataFormat: (row) => {
                return `${row.details.current_stock} ${row.unit.short_name}`;
            }
        },
    ];

    const summaryColumns = [
        ...columns,
        {
            title: t("product.stock_value"),
            dataIndex: "stock_value",
        },
    ];

    const exportColumns = [
        ...columns,
        {
            title: t("product.by_purchase"),
            dataIndex: "stock_value",
            dbKey: "details.current_stock",
            dataFormat: (row) => {
                return formatAmountCurrency(
                    row.details.current_stock *
                    row.details.purchase_price
                );
            }
        },
        {
            title: t("product.by_sales"),
            dataIndex: "stock_value",
            dbKey: "details.current_stock",
            dataFormat: (row) => {
                return formatAmountCurrency(
                    row.details.current_stock *
                    row.details.sales_price
                );
            }
        },
    ];

    return {
        hashableColumns,
        summaryColumns,
        exportColumns,
    }
}

export default fields;

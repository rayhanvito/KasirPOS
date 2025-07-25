import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { formatAmountCurrency } = common();
    const { t } = useI18n();
    const hashableColumns = ['product_id'];

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
            title: t("product.unit_sold"),
            dataIndex: "unit_sold",
            dbKey: "unit_sold",
            dataFormat: (row) => {
                return `${row.unit_sold} ${row.product.unit.short_name}`;
            }
        },
        {
            title: t("product.total_purchase_price"),
            dataIndex: "total_purchase_price",
            dbKey: "total_purchase_price",
            dataFormat: (row) => {
                return formatAmountCurrency(row.unit_sold * row.product.details.purchase_price);
            }
        },
        {
            title: t("product.total_sales_price"),
            dataIndex: "total_sales_price",
            dbKey: "total_sales_price",
            dataFormat: (row) => {
                return formatAmountCurrency(row.total_sales_price);
            }
        },
    ];

    return {
        columns,
        hashableColumns,
    }
}

export default fields;

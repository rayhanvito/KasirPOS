import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const hashableColumns = ['product_id'];
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
            title: t("product.mrp"),
            dataIndex: "mrp",
            dbKey: "details.mrp",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.mrp);
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
    ];

    return {
        columns,
        hashableColumns,
    }
}

export default fields;

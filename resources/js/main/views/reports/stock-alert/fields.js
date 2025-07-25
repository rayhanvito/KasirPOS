import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "products?fields=id,xid,name,image,image_url,item_code,unit_id,x_unit_id,unit{id,xid,short_name},details{product_id,x_product_id,current_stock,stock_quantitiy_alert}";
    const { t } = useI18n();
    const stockAlertHashableColumns = ['products.id'];

    const stockAlertColumns = [
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
            title: t("product.current_stock"),
            dataIndex: "current_stock",
            dbKey: "details.current_stock",
            dataFormat: (row) => {
                return `${row.details.current_stock} ${row.unit.short_name}`
            }
        },
        {
            title: t("product.quantitiy_alert"),
            dataIndex: "stock_quantitiy_alert",
            dbKey: "details.current_stock",
            dataFormat: (row) => {
                return `${row.details.stock_quantitiy_alert} ${row.unit.short_name}`
            }
        },
    ];

    return {
        url,
        stockAlertColumns,
        stockAlertHashableColumns
    }
}

export default fields;

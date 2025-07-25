import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const { formatDateTime, formatAmountCurrency } = common();
    const orderHashableColumns = ['staff_user_id'];

    const salesSummaryColumns = [
        {
            title: t("stock.order_date"),
            dataIndex: "order_date",
            dbKey: "order_date",
            dataFormat: (row) => {
                return formatDateTime(row.order_date);
            }
        },
        {
            title: t("stock.invoice_number"),
            dataIndex: "invoice_number",
            dbKey: "invoice_number",
        },
        {
            title: t("common.party"),
            dataIndex: "user_id",
            dbKey: "user.name",
        },
        {
            title: t("payments.amount"),
            dataIndex: "amount",
            dbKey: "total",
            dataFormat: (row) => {
                return formatAmountCurrency(row.total);
            }
        },
        {
            title: t("payments.payment_status"),
            dataIndex: "payment_status",
            dbKey: 'payment_status'

        },
        {
            title: t("common.created_by"),
            dataIndex: "staff_user_id",
            dbKey: 'staff_member.name'
        },
    ];

    return {
        salesSummaryColumns,
        orderHashableColumns,
    }
}

export default fields;

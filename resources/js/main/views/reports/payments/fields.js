import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const { formatDate, formatAmountCurrency } = common();
    const paymentsHashableColumns = ['user_id'];

    const paymentsColumns = [
        {
            title: t("payments.date"),
            dataIndex: "date",
            dbKey: "date",
            dataFormat: (row) => {
                return formatDate(row.date);
            }
        },
        {
            title: t("payments.payment_number"),
            dataIndex: "payment_number",
            dbKey: "payment_number",
        },
        {
            title: t("payments.payment_type"),
            dataIndex: "payment_type",
            dbKey: "payment_type",
            dataFormat: (row) => {
                return row.payment_type == "in"
                    ? t("menu.payment_in")
                    : t("menu.payment_out")
            }
        },
        {
            title: t("payments.user"),
            dataIndex: "user_id",
            dbKey: "user.name",
        },
        {
            title: t("payment_mode.mode_type"),
            dataIndex: "mode_type",
            dbKey: "payment_mode.name",
        },
        {
            title: t("payments.amount"),
            dataIndex: "amount",
            dbKey: "amount",
            dataFormat: (row) => {
                return formatAmountCurrency(row.amount);
            }
        },
    ];

    return {
        paymentsColumns,
        paymentsHashableColumns,
    }
}

export default fields;

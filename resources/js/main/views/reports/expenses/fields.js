import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const { formatAmountCurrency, formatDate } = common();
    const url = "expenses?fields=id,xid,bill,bill_url,expense_category_id,x_expense_category_id,expenseCategory{id,xid,name},amount,user_id,x_user_id,user{id,xid,name},notes,date";
    const { t } = useI18n();
    const expenseHashableColumns = ['user_id', 'expense_category_id'];

    const expenseColumns = [
        {
            title: t("expense.date"),
            dataIndex: "date",
            dbKey: "date",
            dataFormat: (row) => {
                return formatDate(row.date);
            }
        },
        {
            title: t("expense.expense_category"),
            dataIndex: "expense_category_id",
            dbKey: "expense_category.name",
            dataFormat: (row) => {
                return row.expense_category.name;
            }
        },
        {
            title: t("expense.created_by_user"),
            dataIndex: "user_id",
            dbKey: "user.name",
            dataFormat: (row) => {
                return row.user.name;
            }
        },
        {
            title: t("expense.amount"),
            dataIndex: "amount",
            dbKey: "amount",
            dataFormat: (row) => {
                return formatAmountCurrency(row.amount);
            }
        },
    ];

    return {
        url,
        expenseColumns,
        expenseHashableColumns
    }
}

export default fields;

import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

const fields = () => {
    const userType = ref("customers");
    const { t } = useI18n();
    const { formatAmountCurrency } = common();

    const reportColumns1 = [
        {
            title: t("user.name"),
            dataIndex: "name",
            dbKey: "name",
        },
    ];

    const reportColumns2 = [
        {
            title: t("menu.purchases"),
            dataIndex: "user_details.purchase_order_count",
            dbKey: "details.purchase_order_count",
        },
        {
            title: t("menu.purchase_returns"),
            dataIndex: "user_details.purchase_return_count",
            dbKey: "details.purchase_return_count",
        },
        {
            title: t("menu.sales"),
            dataIndex: "user_details.sales_order_count",
            dbKey: "details.sales_order_count",
        },
        {
            title: t("menu.sales_returns"),
            dataIndex: "user_details.sales_return_count",
            dbKey: "details.sales_return_count",
        },
    ];

    const reportColumns3 = [
        {
            title: t("payments.total_amount"),
            dataIndex: "user_details.total_amount",
            dbKey: "details.total_amount",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.total_amount);
            }
        },
        {
            title: t("payments.paid_amount"),
            dataIndex: "user_details.paid_amount",
            dbKey: "details.paid_amount",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.paid_amount);
            }
        },
        {
            title: t("payments.due_amount"),
            dataIndex: "user_details.due_amount",
            dbKey: "details.due_amount",
            dataFormat: (row) => {
                return formatAmountCurrency(row.details.due_amount);
            }
        },
    ];

    const columns = computed(() => {
        return [
            ...reportColumns1,
            {
                title: userType.value == "customers" ? t("menu.sales") : t("menu.purchases"),
                children: [
                    ...reportColumns2,
                ]
            },
            ...reportColumns3,
        ]
    });

    const exportColumns = computed(() => {
        var reportNew = userType.value == "customers" ? reportColumns2 : reportColumns2;

        return [
            ...reportColumns1,
            ...reportNew,
            ...reportColumns3,
        ]
    });

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name")
        },
        {
            key: "phone",
            value: t("user.phone")
        },
    ];

    return {
        userType,
        columns,
        exportColumns,
        filterableColumns,
    }
}

export default fields;

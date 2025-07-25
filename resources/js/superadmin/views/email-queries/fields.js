import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "superadmin/email-queries?fields=id,xid,date_time,name,email,message,replied";
    const addEditUrl = "/superadmin/email-queries/send-email"
    const { t } = useI18n();

    const initData = {
        email: "",
        subject: "",
        body: "",
    };

    const columns = [
        {
            title: t("email_queries.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("email_queries.name"),
            dataIndex: "name",
        },
        {
            title: t("email_queries.email"),
            dataIndex: "email",
        },
        {
            title: t("email_queries.message"),
            dataIndex: "message",
        },
        {
            title: t("email_queries.replied"),
            dataIndex: "replied",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name")
        },
    ];

    return {
        url,
        addEditUrl,
        columns,
        filterableColumns,
        initData
    }
}

export default fields;

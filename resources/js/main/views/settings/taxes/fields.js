import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "taxes?fields=id,xid,name,rate,tax_type,x_parent_id,parent_id,multipleTax{id,xid,name,rate,tax_type,x_parent_id,parent_id}&filters=parent_id eq null";
    const addEditUrl = "taxes";
    const { t } = useI18n();
    const hashableColumns = ['parent_id'];

    const initData = {
        name: "",
        rate: "",
        tax_type: "single"
    };

    const columns = [
        {
            title: t("tax.name"),
            dataIndex: "name",
        },
        {
            title: t("tax.tax_type"),
            dataIndex: "tax_type",
        },
        {
            title: t("tax.rate"),
            dataIndex: "rate",
        },
     
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("tax.name")
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns
    }
}

export default fields;

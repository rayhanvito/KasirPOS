import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "warehouses";
    const url = "warehouses?fields=id,xid,company_id,x_company_id,logo,logo_url,dark_logo,dark_logo_url,name,slug,email,phone,address,show_email_on_invoice,show_phone_on_invoice,show_mrp_on_invoice,show_discount_tax_on_invoice,terms_condition,bank_details,signature,signature_url,online_store_enabled,default_pos_order_status,customers_visibility,suppliers_visibility,products_visibility";
    const { t } = useI18n();

    const initData = {
        name: "",
        slug: "",
        email: "",
        phone: "",
        logo: undefined,
        logo_url: undefined,
        dark_logo: undefined,
        dark_logo_url: undefined,
        show_email_on_invoice: 0,
        show_phone_on_invoice: 0,
        show_mrp_on_invoice: 1,
        show_discount_tax_on_invoice: 1,
        address: "",
        terms_condition: `1. Goods once sold will not be taken back or exchanged
2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only`,
        bank_details: "",
        signature: undefined,
        signature_url: undefined,
        default_pos_order_status: "delivered",
        customers_visibility: "all",
        suppliers_visibility: "all",
        products_visibility: "all",
    };

    const columns = [
        {
            title: t("warehouse.logo"),
            dataIndex: "logo",
        },
        {
            title: t("warehouse.name"),
            dataIndex: "name",
            sorter: true,
        },
        {
            title: t("warehouse.email"),
            dataIndex: "email",
            sorter: true,
        },
        {
            title: t("warehouse.phone"),
            dataIndex: "phone",
        },
        {
            title: t("warehouse.online_store"),
            dataIndex: "online_store_enabled",
        },

        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("warehouse.name")
        },
        {
            key: "email",
            value: t("warehouse.email")
        },
        {
            key: "phone",
            value: t("warehouse.phone")
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns
    }
}

export default fields;

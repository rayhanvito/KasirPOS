import { useI18n } from "vue-i18n";

const fields = () => {
	const url = "product-cards?fields=xid,title,subtitle,products,x_products,products_details";
	const { t } = useI18n();
	const hashableColumns = ['products'];

	const initData = {
		title: "",
		subtitle: "",
		products: [],
	};

	const columns = [
		{
			title: t("product_card.title"),
			dataIndex: "title",
		},
		{
			title: t("product_card.products"),
			dataIndex: "products",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];

	const filterableColumns = [
		{
			key: "title",
			value: t("product_card.title")
		},
	];

	return {
		url,
		initData,
		columns,
		filterableColumns,
		hashableColumns
	}
}

export default fields;
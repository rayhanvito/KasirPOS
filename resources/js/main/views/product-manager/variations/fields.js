import { useI18n } from "vue-i18n";

const fields = () => {
	const url = "variations?fields=id,xid,name,parent_id,x_parent_id,subVariation{id,xid,parent_id,x_parent_id,name}";
	const addEditUrl = "variations";
	const { t } = useI18n();
    const hashableColumns = ['parent_id']

	const initData = {
		name: "",
        parent_id:undefined
	};

	const columns = [
		{
			title: t("variation.name"),
			dataIndex: "name",
		},
		{
			title: t("variation.value"),
			dataIndex: "value",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];

	const filterableColumns = [
		{
			key: "name",
			value: t("variation.name")
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
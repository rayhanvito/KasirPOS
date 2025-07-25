<template>
	<a-switch
		:checked="status == 1 ? true : false"
		size="small"
		@click="changeStatus"
		:loading="loading"
	/>
</template>

<script>
import { defineComponent } from "vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";

export default defineComponent({
	props: ["x_warehouse_id", "status"],
	emits: ["success"],
	setup(props, { emit }) {
		const { addEditRequestAdmin, loading } = apiAdmin();
		const { t } = useI18n();

		const changeStatus = (checked) => {
			const formData = {
				warehouse_id: props.x_warehouse_id,
				status: checked ? 1 : 0,
			};

			addEditRequestAdmin({
				url: "warehouses/update-online-store-status",
				data: formData,
				successMessage: t("warehouse.online_store_status_updated"),
				success: (res) => {
					emit("success");
					store.dispatch("auth/updateAllWarehouses");
				},
			});
		};

		return {
			changeStatus,
			loading,
		};
	},
});
</script>

<style></style>

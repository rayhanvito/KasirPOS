<template>
	<a-steps :current="current" status="wait">
		<a-step
			v-for="status in salesOrderStatus"
			:key="status.key"
			:title="status.value"
		/>
	</a-steps>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import { findIndex } from "lodash-es";
import common from "../../../../common/composable/common";

export default defineComponent({
	props: ["orderStatus"],
	setup(props) {
		const { salesOrderStatus } = common();
		const { orderStatus } = props;
		const current = ref(0);

		onMounted(() => {
			var indexValue = findIndex(salesOrderStatus, ["key", orderStatus]);

			current.value = indexValue + 1;
		});

		return {
			current,
			salesOrderStatus,
		};
	},
});
</script>

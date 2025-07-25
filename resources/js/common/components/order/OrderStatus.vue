<template>
	<a-tag v-if="data.cancelled" color="#f50">
		{{ $t("common.cancelled") }}
	</a-tag>
	<a-tag :color="orderStatusColors[data.order_status]" v-else>
		{{ $t(`common.${data.order_status}`) }}
	</a-tag>
</template>

<script>
import common from "../../composable/common";

export default {
	props: ["data"],
	setup() {
		const { orderStatusColors } = common();

		return {
			orderStatusColors,
		};
	},
};
</script>

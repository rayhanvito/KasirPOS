<template>
	<a-tabs v-model:activeKey="activeKey">
		<a-tab-pane key="transactions" :tab="$t('payments.transactions')">
			<Transactions
				:user_id="user.xid"
				:result_type="
					user.user_type == 'staff_members'
						? 'staff_member'
						: 'customer_supplier'
				"
			/>
		</a-tab-pane>
	</a-tabs>
</template>

<script>
import { defineComponent, ref } from "vue";
import Transactions from "./Transactions.vue";

export default defineComponent({
	props: ["user"],
	components: {
		Transactions,
	},
	setup() {
		return {
			activeKey: ref("transactions"),
		};
	},
});
</script>

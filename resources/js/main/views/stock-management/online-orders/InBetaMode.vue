<template>
	<div v-if="appEnv != 'envato'">
		<a-alert
			message="Online Store is in beta version"
			type="warning"
			show-icon
			class="mb-20"
		>
			<template #description>
				This feature is in testing mode and right now not available in the script.
				But it will be available in upcoming release. Purchase the script before
				price hike up to $59
				<a href="https://1.envato.market/5bBAWb" target="_blank">
					Buy Now From here
				</a>
			</template>
		</a-alert>
	</div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
	setup() {
		const appEnv = window.config.app_env;

		return {
			appEnv,
		};
	},
});
</script>

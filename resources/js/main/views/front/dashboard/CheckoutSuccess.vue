<template>
	<div class="mt-30 mb-30">
		<a-row type="flex" justify="center">
			<a-col :span="20">
				<a-breadcrumb>
					<a-breadcrumb-item>
						<router-link
							:to="{
								name: 'front.homepage',
								params: { warehouse: frontWarehouse.slug },
							}"
						>
							{{ $t("front.home") }}
						</router-link>
					</a-breadcrumb-item>
					<a-breadcrumb-item>
						<router-link
							:to="{
								name: 'front.dashboard',
								params: { warehouse: frontWarehouse.slug },
							}"
						>
							{{ $t("front.dashboard") }}
						</router-link>
					</a-breadcrumb-item>
					<a-breadcrumb-item>
						<router-link
							:to="{
								name: 'front.orders',
								params: { warehouse: frontWarehouse.slug },
							}"
						>
							{{ $t("front.my_orders") }}
						</router-link>
					</a-breadcrumb-item>
					<a-breadcrumb-item>{{ $t("front.order_placed") }}</a-breadcrumb-item>
				</a-breadcrumb>

				<a-row class="mt-30">
					<a-col :span="24">
						<a-card
							:title="null"
							:bordered="false"
							:style="{ borderRadius: '10px' }"
							class="dashboard-container"
						>
							<a-result
								status="success"
								:title="$t('front.order_placed')"
								:sub-title="
									$t('front.order_placed_message', [orderUniqueId])
								"
							>
								<template #extra>
									<a-space>
										<a-button key="console" type="primary">
											<router-link
												:to="{
													name: 'front.orders',
													params: {
														warehouse: frontWarehouse.slug,
													},
												}"
											>
												<ShoppingOutlined />
												{{ $t("front.all_orders") }}
											</router-link>
										</a-button>
										<a-button key="buy">
											<router-link
												:to="{
													name: 'front.homepage',
													params: {
														warehouse: frontWarehouse.slug,
													},
												}"
											>
												{{ $t("front.continue_shopping") }}
											</router-link>
										</a-button>
									</a-space>
								</template>
							</a-result>
						</a-card>
					</a-col>
				</a-row>
			</a-col>
		</a-row>
	</div>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { ShoppingOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";

export default defineComponent({
	components: {
		ShoppingOutlined,
	},
	setup() {
		const { frontWarehouse } = common();
		const route = useRoute();
		const orderUniqueId = route.params.uniqueId;

		return {
			orderUniqueId,
			frontWarehouse,
		};
	},
});
</script>

<style></style>

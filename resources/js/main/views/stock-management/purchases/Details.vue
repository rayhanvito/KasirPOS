<template>
	<div>
		<AddEdit
			:addEditType="addEditType"
			:visible="addEditVisible"
			:url="addEditUrl"
			@addEditSuccess="addEditSuccess"
			@closed="onCloseAddEdit"
			:formData="formData"
			:data="selectedItem"
			:editItemAmount="editItemAmount"
			:pageTitle="pageTitle"
			:successMessage="successMessage"
		/>

		<div class="order-details">
			<a-row class="mt-10">
				<a-col :xs="24" :sm="24" :md="24" :lg="24">
					<a-descriptions
						:title="null"
						layout="vertical"
						:contentStyle="{ fontWeight: 500, marginBottom: '20px' }"
					>
						<a-descriptions-item :label="$t('stock.order_date')">
							{{ formatDateTime(selectedItem.order_date) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.invoice_number')">
							{{ selectedItem.invoice_number }}
						</a-descriptions-item>
						<a-descriptions-item
							v-if="selectedItem.user && selectedItem.user.name"
							:label="
								selectedItem.order_type == 'purchases' ||
								selectedItem.order_type == 'purchase-returns'
									? $t('stock.supplier')
									: $t('stock.customer')
							"
						>
							{{ selectedItem.user.name }}
						</a-descriptions-item>
						<a-descriptions-item
							v-if="
								selectedItem.order_type == 'stock-transfers' &&
								selectedItem.warehouse &&
								selectedItem.warehouse.name
							"
							:label="$t('stock_transfer.to_warehouse')"
						>
							{{ selectedItem.warehouse.name }}
						</a-descriptions-item>
						<a-descriptions-item
							v-if="
								selectedItem.from_warehouse &&
								selectedItem.from_warehouse.name
							"
							:label="$t('stock_transfer.from_warehouse')"
						>
							{{ selectedItem.from_warehouse.name }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.status')">
							<OrderStatus :data="selectedItem" />
						</a-descriptions-item>
						<a-descriptions-item :label="$t('payments.payment_status')">
							<PaymentStatus :paymentStatus="selectedItem.payment_status" />
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.order_taken_by')">
							{{
								selectedItem.staff_member &&
								selectedItem.staff_member.name
									? selectedItem.staff_member.name
									: "-"
							}}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('payments.total_amount')">
							{{ formatAmountCurrency(selectedItem.total) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('payments.paid_amount')">
							{{ formatAmountCurrency(selectedItem.paid_amount) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('payments.due_amount')">
							{{ formatAmountCurrency(selectedItem.due_amount) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.discount')">
							{{ formatAmountCurrency(selectedItem.discount) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.shipping')">
							{{ formatAmountCurrency(selectedItem.shipping) }}
						</a-descriptions-item>
						<a-descriptions-item :label="$t('stock.order_tax')">
							{{
								selectedItem.tax_rate > 0
									? `${selectedItem.tax_rate}%`
									: "-"
							}}
						</a-descriptions-item>
					</a-descriptions>
				</a-col>
			</a-row>

			<a-row>
				<a-col :span="24">
					<a-tabs v-model:activeKey="activeKey">
						<a-tab-pane
							v-if="
								permsArray.includes('order_payments_view') ||
								permsArray.includes('admin')
							"
							key="payments"
							:tab="$t('payments.payments')"
						>
							<a-row>
								<a-col :span="24">
									<div class="table-responsive">
										<a-table
											:row-key="(record) => record.xid"
											:columns="orderPaymentsColumns"
											:data-source="selectedItem.order_payments"
											:pagination="false"
										>
											<template #bodyCell="{ column, record }">
												<template
													v-if="column.dataIndex === 'date'"
												>
													{{ formatDate(record.payment.date) }}
												</template>
												<template
													v-if="column.dataIndex === 'amount'"
												>
													{{
														formatAmountCurrency(
															record.amount
														)
													}}
												</template>
												<template
													v-if="
														column.dataIndex ===
														'payment_mode_id'
													"
												>
													{{ record.payment.payment_mode.name }}
												</template>
												<template
													v-if="column.dataIndex === 'action'"
												>
													<a-button
														type="primary"
														@click="editItem(record)"
														style="margin-left: 4px"
														v-if="
															permsArray.includes(
																'order_payments_edit'
															) ||
															permsArray.includes('admin')
														"
													>
														<template #icon
															><EditOutlined
														/></template>
													</a-button>
													<a-button
														type="primary"
														@click="
															showDeleteConfirm(record.xid)
														"
														style="margin-left: 4px"
														v-if="
															permsArray.includes(
																'order_payments_delete'
															) ||
															permsArray.includes('admin')
														"
													>
														<template #icon
															><DeleteOutlined
														/></template>
													</a-button>
												</template>
											</template>
										</a-table>
									</div>
								</a-col>
							</a-row>
						</a-tab-pane>
						<a-tab-pane key="order_items" :tab="$t('product.order_items')">
							<a-row>
								<a-col :span="24">
									<div class="table-responsive">
										<a-table
											:row-key="(record) => record.xid"
											:columns="orderItemDetailsColumns"
											:data-source="selectedItem.items"
											:pagination="false"
										>
											<template #bodyCell="{ column, record }">
												<template
													v-if="
														column.dataIndex === 'product_id'
													"
												>
													<a-badge>
														<a-avatar
															shape="square"
															:src="
																record.product.image_url
															"
														/>
														{{ record.product.name }}
													</a-badge>
												</template>
												<template
													v-if="
														column.dataIndex ===
														'single_unit_price'
													"
												>
													{{
														formatAmountCurrency(
															record.single_unit_price
														)
													}}
												</template>
												<template
													v-if="
														column.dataIndex ===
														'total_discount'
													"
												>
													{{
														formatAmountCurrency(
															record.total_discount
														)
													}}
												</template>
												<template
													v-if="
														column.dataIndex === 'total_tax'
													"
												>
													{{
														formatAmountCurrency(
															record.total_tax
														)
													}}
												</template>
												<template
													v-if="column.dataIndex === 'subtotal'"
												>
													{{
														formatAmountCurrency(
															record.subtotal
														)
													}}
												</template>
											</template>
										</a-table>
									</div>
								</a-col>
							</a-row>
						</a-tab-pane>
					</a-tabs>
				</a-col>
			</a-row>
		</div>
	</div>
</template>

<script>
import { ref, createVNode, computed } from "vue";
import {
	LeftOutlined,
	PlusOutlined,
	EditOutlined,
	DeleteOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import fields from "./fields";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import AddEdit from "../order-payments/AddEdit.vue";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import OrderStatus from "../../../../common/components/order/OrderStatus.vue";

export default {
	props: ["selectedItem"],
	components: {
		PlusOutlined,
		EditOutlined,
		DeleteOutlined,
		LeftOutlined,
		UserInfo,
		AddEdit,
		PaymentStatus,
		OrderStatus,
	},
	setup(props, { emit }) {
		const {
			initPaymentData,
			orderPaymentsColumns,
			orderItemDetailsColumns,
		} = fields();
		const { formatAmountCurrency, permsArray, formatDate, formatDateTime } = common();
		const { t } = useI18n();

		const addEditVisible = ref(false);
		const addEditType = ref("add");
		const addEditUrl = ref("order-payments");
		const formData = ref({});
		const viewData = ref({});
		const editItemAmount = ref(0);
		const activeKeyName =
			permsArray.value.includes("order_payments_view") ||
			permsArray.value.includes("admin")
				? "payments"
				: "order_items";
		const activeKey = ref(activeKeyName);

		const pageTitle = computed(() => {
			return addEditType.value == "add" ? t(`payments.add`) : t(`payments.edit`);
		});

		const successMessage = computed(() => {
			return addEditType.value == "add"
				? t(`payments.created`)
				: t(`payments.updated`);
		});

		const onClose = () => {
			emit("goBack");
		};

		const addItem = () => {
			addEditUrl.value = `order-payments`;
			addEditType.value = "add";
			formData.value = { ...initPaymentData, order_id: props.selectedItem.xid };
			addEditVisible.value = true;
			editItemAmount.value = 0;
		};

		const addEditSuccess = (id) => {
			emit("reloadOrder");

			// If add action is performed then move page to first
			if (addEditType.value == "add") {
				formData.value = {
					order_id: props.selectedItem.xid,
					date: undefined,
					payment_mode_id: undefined,
					amount: "",
					notes: "",
				};
			}
			addEditVisible.value = false;
		};

		const onCloseAddEdit = () => {
			formData.value = { ...initPaymentData, order_id: props.selectedItem.xid };
			addEditVisible.value = false;
			editItemAmount.value = 0;
		};

		return {
			onClose,

			formatDate,
			formatDateTime,
			formatAmountCurrency,
			orderPaymentsColumns,
			orderItemDetailsColumns,
			activeKey,
			addEditSuccess,
			formData,
			addEditVisible,
			addItem,
			onCloseAddEdit,
			addEditUrl,
			addEditType,
			viewData,
			permsArray,
			editItemAmount,

			pageTitle,
			successMessage,
		};
	},
};
</script>

<style lang="less">
.order-details {
	.ant-descriptions-item {
		padding-bottom: 5px;
	}
}
</style>

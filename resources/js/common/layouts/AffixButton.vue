<template>
	<div :style="affixStyle">
		<a-dropdown
			:placement="
				appSetting.rtl
					? position == 'top'
						? 'bottomLeft'
						: 'topLeft'
					: position == 'top'
					? 'bottomRight'
					: 'topRight'
			"
		>
			<a-button
				v-if="position == 'bottom'"
				type="primary"
				shape="circle"
				:style="{ width: '50px', height: '50px' }"
			>
				<template #icon><PlusOutlined /></template>
			</a-button>
			<a-button v-else type="link" shape="round" :style="{ padding: '0px' }">
				<template #icon><PlusCircleOutlined /></template>
			</a-button>
			<template #overlay>
				<a-menu>
					<StaffAddButton
						v-if="addMenus.includes('staff_member')"
						customType="menu"
					>
						<template #icon>
							<TeamOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_staff_member") }}</span>
					</StaffAddButton>

					<CustomerAddButton
						v-if="addMenus.includes('customer')"
						customType="menu"
					>
						<template #icon>
							<SolutionOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_customer") }}</span>
					</CustomerAddButton>

					<SupplierAddButton
						v-if="addMenus.includes('supplier')"
						customType="menu"
					>
						<template #icon>
							<UserOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_supplier") }}</span>
					</SupplierAddButton>

					<BrandAddButton v-if="addMenus.includes('brand')" customType="menu">
						<template #icon>
							<CoffeeOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_brand") }}</span>
					</BrandAddButton>

					<CategoryAddButton
						v-if="addMenus.includes('category')"
						customType="menu"
					>
						<template #icon>
							<BarsOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_category") }}</span>
					</CategoryAddButton>

					<ProductAddButton
						v-if="addMenus.includes('product')"
						customType="menu"
					>
						<template #icon>
							<AppstoreOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_product") }}</span>
					</ProductAddButton>

					<a-menu-item
						@click="
							() => {
								menuSelected();
								$router.push({
									name: 'admin.stock.sales.create',
								});
							}
						"
						key="sales"
						v-if="
							(permsArray.includes('sales_create') ||
								permsArray.includes('admin')) &&
							addMenus.includes('sales')
						"
					>
						<ShopOutlined />
						{{ $t("topbar_add_button.add_sales") }}
					</a-menu-item>

					<a-menu-item
						@click="
							() => {
								menuSelected();
								$router.push({
									name: 'admin.stock.purchases.create',
								});
							}
						"
						key="purchases"
						v-if="
							(permsArray.includes('purchases_create') ||
								permsArray.includes('admin')) &&
							addMenus.includes('purchase')
						"
					>
						<ShoppingOutlined />
						{{ $t("topbar_add_button.add_purchase") }}
					</a-menu-item>

					<ExpenseCategoryAddButton
						v-if="addMenus.includes('expense_category')"
						customType="menu"
					>
						<template #icon>
							<CreditCardOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_expense_cateogory") }}</span>
					</ExpenseCategoryAddButton>

					<ExpenseAddButton
						v-if="addMenus.includes('expense')"
						customType="menu"
					>
						<template #icon>
							<WalletOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_expense") }}</span>
					</ExpenseAddButton>

					<CurrencyAddButton
						v-if="addMenus.includes('currency')"
						customType="menu"
					>
						<template #icon>
							<DollarOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_currency") }}</span>
					</CurrencyAddButton>

					<WarehouseAddButton
						v-if="addMenus.includes('warehouse')"
						customType="menu"
					>
						<template #icon>
							<HddOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_warehouse") }}</span>
					</WarehouseAddButton>

					<UnitAddButton v-if="addMenus.includes('unit')" customType="menu">
						<template #icon>
							<ApartmentOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_unit") }}</span>
					</UnitAddButton>

					<LanguageAddButton
						v-if="addMenus.includes('language')"
						customType="menu"
					>
						<template #icon>
							<TranslationOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_language") }}</span>
					</LanguageAddButton>

					<RoleAddButton v-if="addMenus.includes('role')" customType="menu">
						<template #icon>
							<FundViewOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_role") }}</span>
					</RoleAddButton>

					<TaxAddButton v-if="addMenus.includes('tax')" customType="menu">
						<template #icon>
							<ScheduleOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_tax") }}</span>
					</TaxAddButton>

					<PaymentModeAddButton
						v-if="addMenus.includes('payment_mode')"
						customType="menu"
					>
						<template #icon>
							<AccountBookOutlined />
						</template>
						<span>{{ $t("topbar_add_button.add_payment_mode") }}</span>
					</PaymentModeAddButton>
				</a-menu>
			</template>
		</a-dropdown>
	</div>
</template>

<script>
import { computed } from "vue";
import {
	PlusOutlined,
	TeamOutlined,
	UserOutlined,
	SolutionOutlined,
	AppstoreOutlined,
	ShopOutlined,
	ShoppingOutlined,
	WalletOutlined,
	PlusCircleOutlined,
	BarsOutlined,
	CoffeeOutlined,
	CreditCardOutlined,
	DollarOutlined,
	HddOutlined,
	ApartmentOutlined,
	TranslationOutlined,
	FundViewOutlined,
	AccountBookOutlined,
	ScheduleOutlined,
} from "@ant-design/icons-vue";
import common from "../composable/common";
import StaffAddButton from "../../main/views/users/StaffAddButton.vue";
import CustomerAddButton from "../../main/views/users/CustomerAddButton.vue";
import SupplierAddButton from "../../main/views/users/SupplierAddButton.vue";
import BrandAddButton from "../../main/views/product-manager/brands/AddButton.vue";
import CategoryAddButton from "../../main/views/product-manager/categories/AddButton.vue";
import ProductAddButton from "../../main/views/product-manager/products/AddButton.vue";
import ExpenseAddButton from "../../main/views/expense-manager/expenses/AddButton.vue";
import ExpenseCategoryAddButton from "../../main/views/expense-manager/expense-categories/AddButton.vue";
import CurrencyAddButton from "../../main/views/common/settings/currency/AddButton.vue";
import WarehouseAddButton from "../../main/views/settings/warehouses/AddButton.vue";
import UnitAddButton from "../../main/views/settings/units/AddButton.vue";
import LanguageAddButton from "../../main/views/common/settings/translations/langs/AddButton.vue";
import RoleAddButton from "../../main/views/settings/roles/AddButton.vue";
import TaxAddButton from "../../main/views/settings/taxes/AddButton.vue";
import PaymentModeAddButton from "../../main/views/settings/payment-modes/AddButton.vue";

export default {
	props: {
		position: {
			default: "bottom",
		},
	},
	components: {
		PlusOutlined,
		TeamOutlined,
		UserOutlined,
		AppstoreOutlined,
		ShopOutlined,
		ShoppingOutlined,
		WalletOutlined,
		PlusCircleOutlined,
		SolutionOutlined,
		BarsOutlined,
		CoffeeOutlined,
		CreditCardOutlined,
		DollarOutlined,
		HddOutlined,
		ApartmentOutlined,
		TranslationOutlined,
		FundViewOutlined,
		AccountBookOutlined,
		ScheduleOutlined,

		StaffAddButton,
		CustomerAddButton,
		SupplierAddButton,
		BrandAddButton,
		CategoryAddButton,
		ProductAddButton,
		ExpenseCategoryAddButton,
		ExpenseAddButton,
		CurrencyAddButton,
		WarehouseAddButton,
		UnitAddButton,
		LanguageAddButton,
		RoleAddButton,
		TaxAddButton,
		PaymentModeAddButton,
	},
	setup(props, { emit }) {
		const { permsArray, appSetting, addMenus } = common();
		const menuSelected = () => {};
		const affixStyle = computed(() => {
			if (props.position == "bottom") {
				if (appSetting.value.rtl) {
					return { position: "fixed", left: "40px", bottom: "30px" };
				} else {
					return { position: "fixed", right: "40px", bottom: "30px" };
				}
			} else {
				return {};
			}
		});

		return {
			permsArray,
			appSetting,
			addMenus,
			menuSelected,
			affixStyle,

			innerWidth: window.innerWidth,
		};
	},
};
</script>

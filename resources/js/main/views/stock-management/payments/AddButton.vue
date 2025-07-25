<template>
	<div
		v-if="
			permsArray.includes(
				paymentType == 'in' ? 'payment_in_create' : 'payment_out_create'
			) || permsArray.includes('admin')
		"
	>
		<template v-if="customType == 'menu'">
			<a-menu-item @click="showAdd" key="payments">
				<template #icon>
					<slot name="icon"></slot>
				</template>
				<slot></slot>
			</a-menu-item>
		</template>
		<template v-else>
			<a-tooltip
				v-if="tooltip"
				placement="topLeft"
				:title="$t('payments.add')"
				arrow-point-at-center
			>
				<a-button @click="showAdd" class="ml-5 no-border-radius" :type="btnType">
					<template #icon> <PlusOutlined /> </template>
					<slot></slot>
				</a-button>
			</a-tooltip>
			<a-button
				v-else
				@click="showAdd"
				class="ml-5 no-border-radius"
				:type="btnType"
			>
				<template #icon> <PlusOutlined /> </template>
				<slot></slot>
			</a-button>
		</template>

		<AddEdit
			:addEditType="addEditType"
			:visible="visible"
			:url="addEditUrl"
			@addEditSuccess="addEditSuccess"
			@closed="onClose"
			:formData="formData"
			:data="formData"
			:pageTitle="$t('payments.add')"
			:successMessage="$t('payments.created')"
		/>
	</div>
</template>
<script>
import { defineComponent, ref } from "vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import AddEdit from "./AddEdit.vue";
import fields from "./fields";
import common from "../../../../common/composable/common";

export default defineComponent({
	props: {
		btnType: {
			default: "default",
		},
		tooltip: {
			default: true,
		},
		customType: {
			default: "button",
		},
	},
	emits: ["onAddSuccess"],
	components: {
		PlusOutlined,
		AddEdit,
	},
	setup(props, { emit }) {
		const { permsArray } = common();
		const { initData, addEditUrl, paymentType } = fields();
		const visible = ref(false);
		const addEditType = ref("add");
		const formData = ref({ ...initData });

		const showAdd = () => {
			visible.value = true;
		};

		const addEditSuccess = () => {
			visible.value = false;
			formData.value = { ...initData };
			emit("onAddSuccess");
		};

		const onClose = () => {
			visible.value = false;
		};

		return {
			permsArray,
			visible,
			addEditType,
			addEditUrl,
			paymentType,
			formData,
			addEditSuccess,
			onClose,
			showAdd,
		};
	},
});
</script>

<template>
	<a-list item-layout="horizontal" :data-source="userAddresses">
		<template #renderItem="{ item }">
			<a-list-item>
				<template #actions>
					<a-button @click="editAddress(item)" class="p-0" type="link">
						{{ $t("common.edit") }}
					</a-button>
					<a-button @click="deleteAddress(item.xid)" class="p-0" type="link">
						{{ $t("common.delete") }}
					</a-button>
				</template>
				<a-list-item-meta>
					<template #title>
						<a-radio-group
							v-model:value="selectedAddress"
							name="addressCheckbox"
							@change="addressSelected"
						>
							<a-radio :value="item.xid">
								{{ item.name }}
							</a-radio>
						</a-radio-group>
					</template>
					<template #description>
						{{ buildAddress(item) }}<br />
						{{ $t("user.phone") }}:
						{{ item.phone }}
					</template>
				</a-list-item-meta>
			</a-list-item>
		</template>
	</a-list>
	<a-button class="pl-0" type="link" @click="addAddress">
		{{ $t("front.add_new_address") }}
	</a-button>
	<AddEdit
		:addEditVisible="addEditVisible"
		:formData="addEditAddress"
		:addEditId="addEditId"
		@addEditSuccess="addEditSuccess"
	/>
</template>

<script>
import { defineComponent, ref, onMounted, createVNode } from "vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { useI18n } from "vue-i18n";
import { Modal, notification } from "ant-design-vue";
import AddEdit from "./AddEdit.vue";
import { buildAddress } from "../../../../../common/scripts/functions";

export default defineComponent({
	props: [],
	emits: ["onAddressSelection"],
	components: {
		AddEdit,
	},
	setup(props, { emit }) {
		const selectedAddress = ref(null);
		const userAddresses = ref([]);
		const store = useStore();
		const { t } = useI18n();
		const user = store.state.front.user;
		const indexUrl =
			"front/self/address?fields=id,xid,name,email,phone,address,shipping_address,city,state,country,zipcode";
		const addEditVisible = ref(false);
		const addEditAddress = ref({});
		const addEditId = ref(null);

		onMounted(() => {
			getUserAddress();
			selectedAddress.value = props.selectedAddress;
		});

		const getUserAddress = () => {
			axiosFront.get(indexUrl).then((response) => {
				userAddresses.value = response.data;
			});
		};

		const addAddress = () => {
			addEditAddress.value = {
				name: user.name,
				phone: user.phone,
				email: user.email,
				address: user.address,
				shipping_address: user.shipping_address,
				city: user.city,
				state: user.state,
				country: user.country,
				zipcode: user.zipcode,
			};
			addEditId.value = null;
			addEditVisible.value = true;
		};

		const editAddress = (address) => {
			addEditAddress.value = {
				name: address.name,
				email: address.email,
				phone: address.phone,
				address: address.address,
				shipping_address: address.shipping_address,
				city: address.city,
				state: address.state,
				country: address.country,
				zipcode: address.zipcode,
				_method: "PUT",
			};
			addEditId.value = address.xid;
			addEditVisible.value = true;
		};

		const addressSelected = () => {
			emit("onAddressSelection", selectedAddress.value);
		};

		const addEditSuccess = () => {
			getUserAddress();
		};

		const deleteAddress = (userAddressId) => {
			Modal.confirm({
				title: t("common.delete") + "?",
				icon: createVNode(ExclamationCircleOutlined),
				content: t("front.address_delete_message"),
				centered: true,
				okText: t("common.yes"),
				okType: "danger",
				cancelText: t("common.no"),
				onOk() {
					axiosFront
						.delete(`front/self/address/${userAddressId}`)
						.then((response) => {
							getUserAddress();

							notification.success({
								message: t("common.success"),
								description: t("front.address_deleted"),
								placement: "bottomRight",
							});
						});
				},
				onCancel() {},
			});
		};

		return {
			userAddresses,
			addAddress,
			editAddress,
			selectedAddress,
			addressSelected,

			buildAddress,
			addEditVisible,
			addEditAddress,
			addEditId,
			addEditSuccess,
			deleteAddress,
		};
	},
});
</script>

<style></style>

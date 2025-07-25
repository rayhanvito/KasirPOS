<template>
	<div class="mt-30 mb-30">
		<a-row type="flex" justify="center">
			<a-col :span="20">
				<a-breadcrumb>
					<a-breadcrumb-item>
						<router-link :to="{ name: 'front.homepage' }">
							{{ $t("front.home") }}
						</router-link>
					</a-breadcrumb-item>
					<a-breadcrumb-item>
						<router-link :to="{ name: 'front.dashboard' }">
							{{ $t("front.dashboard") }}
						</router-link>
					</a-breadcrumb-item>
					<a-breadcrumb-item>{{ $t("front.profile") }}</a-breadcrumb-item>
				</a-breadcrumb>

				<a-row :gutter="[30, 30]" class="mt-30">
					<a-col :xs="24" :sm="24" :md="24" :lg="6" :xl="6">
						<dashboard-sidebar />
					</a-col>
					<a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
						<a-card
							:title="null"
							:bordered="false"
							:style="{ borderRadius: '10px' }"
						>
							<a-row>
								<a-col :span="24">
									<a-page-header
										:title="$t('front.profile_settings')"
										style="padding-left: 0px"
									/>
								</a-col>
							</a-row>

							<a-form layout="vertical">
								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="12" :lg="12">
										<a-form-item
											:label="$t('user.name')"
											name="name"
											:help="rules.name ? rules.name.message : null"
											:validateStatus="rules.name ? 'error' : null"
										>
											<a-input
												v-model:value="formData.name"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.name')]
													)
												"
											/>
										</a-form-item>
									</a-col>
									<a-col :xs="24" :sm="24" :md="12" :lg="12">
										<a-form-item
											:label="$t('user.email')"
											name="email"
											:help="
												rules.email ? rules.email.message : null
											"
											:validateStatus="rules.email ? 'error' : null"
										>
											<a-input
												:value="formData.email"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.email')]
													)
												"
												disabled
											/>
										</a-form-item>
									</a-col>
								</a-row>

								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="12" :lg="12">
										<a-form-item
											:label="$t('user.phone')"
											name="phone"
											:help="
												rules.phone ? rules.phone.message : null
											"
											:validateStatus="rules.phone ? 'error' : null"
										>
											<a-input
												v-model:value="formData.phone"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.phone')]
													)
												"
											/>
										</a-form-item>
									</a-col>
									<a-col :xs="24" :sm="24" :md="12" :lg="12">
										<a-form-item
											:label="$t('user.password')"
											name="password"
											:help="
												rules.password
													? rules.password.message
													: null
											"
											:validateStatus="
												rules.password ? 'error' : null
											"
										>
											<a-input
												v-model:value="formData.password"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.password')]
													)
												"
												type="password"
												autocomplete="off"
											/>
											<small class="small-text-message">
												{{ $t("user.password_blank") }}
											</small>
										</a-form-item>
									</a-col>
								</a-row>

								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="24" :lg="24">
										<a-form-item
											:label="$t('user.profile_image')"
											name="profile_image"
											:help="
												rules.profile_image
													? rules.profile_image.message
													: null
											"
											:validateStatus="
												rules.profile_image ? 'error' : null
											"
										>
											<UploadFront
												:formData="formData"
												folder="user"
												imageField="profile_image"
												@onFileUploaded="
													(file) => {
														formData.profile_image =
															file.file;
														formData.profile_image_url =
															file.file_url;
													}
												"
											/>
										</a-form-item>
									</a-col>
								</a-row>

								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="24" :lg="24">
										<a-form-item
											:label="$t('user.billing_address')"
											name="address"
											:help="
												rules.address
													? rules.address.message
													: null
											"
											:validateStatus="
												rules.address ? 'error' : null
											"
										>
											<a-textarea
												v-model:value="formData.address"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.billing_address')]
													)
												"
												:auto-size="{ minRows: 2, maxRows: 3 }"
											/>
										</a-form-item>
									</a-col>
								</a-row>

								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="24" :lg="24">
										<a-form-item
											:label="$t('user.shipping_address')"
											name="shipping_address"
											:help="
												rules.shipping_address
													? rules.shipping_address.message
													: null
											"
											:validateStatus="
												rules.shipping_address ? 'error' : null
											"
										>
											<a-textarea
												v-model:value="formData.shipping_address"
												:placeholder="
													$t(
														'common.placeholder_default_text',
														[$t('user.shipping_address')]
													)
												"
												:auto-size="{ minRows: 2, maxRows: 3 }"
											/>
										</a-form-item>
									</a-col>
								</a-row>

								<a-row :gutter="16">
									<a-col :xs="24" :sm="24" :md="24" :lg="24">
										<a-form-item>
											<a-button type="primary" @click="onSubmit">
												<template #icon>
													<SaveOutlined />
												</template>
												{{ $t("common.update") }}
											</a-button>
										</a-form-item>
									</a-col>
								</a-row>
							</a-form>
						</a-card>
					</a-col>
				</a-row>
			</a-col>
		</a-row>
	</div>
</template>

<script>
import { onMounted, reactive, computed, ref, createVNode } from "vue";
import {
	EyeOutlined,
	PlusOutlined,
	EditOutlined,
	DeleteOutlined,
	ExclamationCircleOutlined,
	SaveOutlined,
} from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import processRequestFront from "../../../../common/plugins/processRequestFront";
import UploadFront from "../../../../common/core/ui/file/UploadFront.vue";
import DashboardSidebar from "../includes/DashboardSidebar.vue";

export default {
	components: {
		EyeOutlined,
		PlusOutlined,
		EditOutlined,
		DeleteOutlined,
		ExclamationCircleOutlined,
		SaveOutlined,

		DashboardSidebar,
		UploadFront,
	},
	setup() {
		const { t } = useI18n();
		const store = useStore();
		const formData = ref({});
		const rules = ref({});
		const currencies = ref({});
		const user = store.state.front.user;

		const formImage = ref({
			image: "",
			image_url: "",
		});

		onMounted(() => {
			formData.value = {
				name: user.name,
				email: user.email,
				password: "",
				phone: user.phone,
				address: user.address,
				shipping_address: user.shipping_address,
				city: user.city,
				state: user.state,
				country: user.country,
				zipcode: user.zipcode,
				profile_image: user.profile_image,
				profile_image_url: user.profile_image_url,
			};
		});

		const onSubmit = () => {
			processRequestFront({
				url: "front/profile",
				data: formData.value,
				success: (res) => {
					store.commit("front/updateUser", res.user);

					// Toastr Notificaiton
					notification.success({
						placement: "bottomRight",
						message: t("common.success"),
						description: t("user.profile_updated"),
					});

					rules.value = {};
				},
				error: (errorRules) => {
					rules.value = errorRules;
					message.error(t("common.fix_errors"));
				},
			});
		};

		return {
			formData,
			rules,
			formImage,
			currencies,
			onSubmit,
		};
	},
};
</script>

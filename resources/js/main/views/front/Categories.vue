<template>
	<div class="bg-white">
		<a-row type="flex" justify="center">
			<a-col :span="20">
				<a-row :gutter="[30, 30]" class="mt-30">
					<a-col
						:xs="24"
						:sm="24"
						:md="24"
						:lg="6"
						:xl="6"
						style="padding-left: 0px"
					>
						<LeftSidebarMenu
							:catSelectedKeys="catSelectedKeys"
							class="categories-page-lefbar"
						/>
					</a-col>
					<a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
						<a-row class="mt-30 mb-30 category-page-container">
							<a-col :span="24">
								<a-page-header style="padding-left: 0px">
									<template #title>
										<span v-if="category && category.name">
											{{ category.name }}
										</span>
										<span v-else>
											{{ $t("front_setting.all_categories") }}
										</span>
									</template>
									<template #breadcrumb>
										<a-breadcrumb>
											<a-breadcrumb-item>
												<router-link
													:to="{ name: 'front.homepage' }"
												>
													{{ $t("front.home") }}
												</router-link>
											</a-breadcrumb-item>
											<a-breadcrumb-item>
												<router-link
													:to="{ name: 'front.categories' }"
												>
													{{ $t("menu.categories") }}
												</router-link>
											</a-breadcrumb-item>
											<a-breadcrumb-item
												v-if="category && category.name"
											>
												{{ category.name }}
											</a-breadcrumb-item>
										</a-breadcrumb>
									</template>
								</a-page-header>
								<a-divider class="mt-0" />

								<a-row
									:gutter="[30, 30]"
									v-if="products && products.length > 0"
								>
									<a-col
										v-for="product in products"
										:xs="24"
										:sm="12"
										:md="8"
										:lg="6"
										:xl="6"
										:key="product.id"
									>
										<ProductCard
											:product="product"
											:key="product.id"
										/>
									</a-col>
								</a-row>
								<a-row :gutter="30" class="mt-30 mb-30" v-else>
									<a-col :span="24">
										<a-empty
											:description="$t('front_setting.no_results')"
										/>
									</a-col>
								</a-row>
								<a-row
									:gutter="30"
									class="mt-30 mb-30"
									v-if="products && products.length > 0"
								>
									<a-col :span="24">
										<a-pagination
											v-model:current="currentPage"
											v-model:pageSize="pageSize"
											:total="totalRecords"
											@change="paginationClicked"
											:showSizeChanger="false"
										/>
									</a-col>
								</a-row>
							</a-col>
						</a-row>
					</a-col>
				</a-row>
			</a-col>
		</a-row>
	</div>
</template>
<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import { useRoute } from "vue-router";
import LeftSidebarMenu from "./layouts/LeftSidebarMenu.vue";
import ProductCard from "./components/ProductCard.vue";

export default defineComponent({
	components: { LeftSidebarMenu, ProductCard },
	setup() {
		const categoriesSelectedKeys = ref([]);
		const categoriesOpenKeys = ref([]);
		const products = ref([]);
		const route = useRoute();
		const category = ref({});
		const catSelectedKeys = ref([]);
		const totalRecords = ref(1);
		const currentPage = ref(1);
		const pageSize = ref(20);

		onMounted(() => {
			const params = route.params;
			getData(params);
		});

		const getData = (params) => {
			if (params && params.slug) {
				const slugParamsArray = params.slug;
				const categorySlug = slugParamsArray[slugParamsArray.length - 1];

				axiosFront
					.post(`/front/category-by-slug/${categorySlug}`)
					.then((response) => {
						category.value = response.data.category;
						getProducts(category.value.id);

						catSelectedKeys.value = [category.value.id];
					});
			} else {
				catSelectedKeys.value = [];
				category.value = {};
				getProducts();
			}
		};

		const getProducts = () => {
			let url =
				"products?fields=id,xid,name,slug,image,image_url,description,category_id,x_category_id,category{id,xid,name,slug},brand_id,x_brand_id,brand{id,xid,name,slug},unit_id,x_unit_id,unit{id,xid,name,short_name},description,details{stock_quantitiy_alert,opening_stock,opening_stock_date,wholesale_price,wholesale_quantity,mrp,purchase_price,sales_price,tax_id,x_tax_id,purchase_tax_type,sales_tax_type,current_stock,warehouse_id,x_warehouse_id,status},details:tax{id,xid,name,rate}";
			if (category.value && category.value.id) {
				const categoryId = category.value.id;
				url += `&filters=category_id eq ${categoryId}`;
			}

			const limit = pageSize.value;
			const offset = (currentPage.value - 1) * limit;

			url += `&offset=${offset}&limit=${limit}`;

			axiosFront.get(url).then((response) => {
				totalRecords.value = response.meta.paging.total;
				products.value = response.data;
			});
		};

		const paginationClicked = (page, perPage) => {
			currentPage.value = page;
			pageSize.value = perPage;

			getProducts();
		};

		watch(route, (newVal, oldVal) => {
			getData(newVal.params);
		});

		return {
			catSelectedKeys,
			category,
			categoriesSelectedKeys,
			categoriesOpenKeys,
			products,

			currentPage,
			pageSize,
			totalRecords,
			paginationClicked,
		};
	},
});
</script>

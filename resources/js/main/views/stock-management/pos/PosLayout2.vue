<template>
	<a-row :gutter="[16, 16]">
		<a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
			<a-select
				v-model:value="formData.brand_id"
				:placeholder="$t('common.select_default_text', [$t('product.brand')])"
				:allowClear="true"
				style="width: 100%"
				optionFilterProp="title"
				show-search
				@change="reFetchProducts"
			>
				<a-select-option
					v-for="brand in brands"
					:key="brand.xid"
					:title="brand.name"
					:value="brand.xid"
				>
					{{ brand.name }}
				</a-select-option>
			</a-select>
		</a-col>
		<a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
			<a-input-group compact>
				<a-select
					v-model:value="formData.category_id"
					:placeholder="
						$t('common.select_default_text', [$t('product.category')])
					"
					:allowClear="true"
					style="width: 100%"
					optionFilterProp="title"
					show-search
					@change="reFetchProducts"
				>
					<a-select-option
						v-for="category in categories"
						:key="category.xid"
						:title="category.name"
						:value="category.xid"
					>
						{{ category.name }}
					</a-select-option>
				</a-select>
			</a-input-group>
		</a-col>
	</a-row>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
	props: ["formData", "brands", "categories"],
	emits: ["changed"],
	setup(props, { emit }) {
		const reFetchProducts = () => {
			emit("changed");
		};

		return {
			reFetchProducts,
		};
	},
});
</script>

<style></style>

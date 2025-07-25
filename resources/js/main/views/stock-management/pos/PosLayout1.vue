<template>
	<a-row :gutter="[16, 16]">
		<a-col
			:xs="24"
			:sm="24"
			:md="24"
			:lg="24"
			:xl="24"
			class="pos-style-1-category-scroll"
		>
			<div
				@click="
					() => {
						formData.category_id = undefined;
						reFetchProducts();
					}
				"
				class="pos-style-1-category-box"
			>
				<a-avatar
					shape="square"
					size="large"
					:style="{
						backgroundColor: '#fff',
						color: 'black',
						verticalAlign: 'middle',
						marginTop: '5px',
					}"
				>
					<template #icon><ContainerOutlined /></template>
				</a-avatar>
				<p>{{ $t("common.all") }}</p>
			</div>

			<div
				v-for="category in categories"
				:key="category.xid"
				@click="
					() => {
						formData.category_id = category.xid;
						reFetchProducts();
					}
				"
				class="pos-style-1-category-box"
			>
				<a-tooltip :title="category.name">
					<a-avatar
						size="large"
						:src="category.image_url"
						:style="{
							verticalAlign: 'middle',
							marginTop: '5px',
						}"
					/>
					<p>{{ getCategoryName(category.name) }}</p>
				</a-tooltip>
			</div>
		</a-col>
	</a-row>
</template>

<script>
import { defineComponent } from "vue";
import { ContainerOutlined } from "@ant-design/icons-vue";

export default defineComponent({
	props: ["formData", "brands", "categories"],
	emits: ["changed"],
	components: {
		ContainerOutlined,
	},
	setup(props, { emit }) {
		const reFetchProducts = () => {
			emit("changed");
		};

		const getCategoryName = (cateogryName) => {
			if (cateogryName.length > 9) {
				return cateogryName.substring(0, 8) + "..";
			} else {
				return cateogryName;
			}
		};

		return {
			reFetchProducts,
			getCategoryName,
		};
	},
});
</script>

<style lang="less">
.pos-style-1-category-scroll {
	overflow-x: auto;
	white-space: nowrap;
}

.pos-style-1-category-box {
	background: #fff;
	border: 3px solid #e8e8e8;
	display: inline-block;
	margin-right: 15px;
	text-align: center;
	border-radius: 5px;
	margin-bottom: 5px;
	width: 80px;
	height: 80px;
	cursor: pointer;
}

.pos-style-1-cateogry-image {
	width: 80px;
	height: 80px;

	img {
		width: 50px;
		height: 50px;
	}
}
</style>

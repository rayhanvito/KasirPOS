<template>
	<div :style="{ background: '#fff' }" class="category-box">
		<a-row type="flex" justify="center">
			<a-col :span="20">
				<a-row :gutter="[16, 16]">
					<a-col
						v-for="category in categories"
						:key="category.id"
						:xs="24"
						:sm="24"
						:md="6"
						:lg="3"
						:xl="2"
					>
						<div class="category">
							<img
								:alt="category.name"
								:src="category.image_url"
								width="80"
							/>
							<h4>
								{{ category.name }}
							</h4>
						</div>
					</a-col>
				</a-row>
			</a-col>
		</a-row>
	</div>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";

export default defineComponent({
	setup() {
		const categories = ref([]);

		onMounted(() => {
			axiosFront.get("front/homepage").then((response) => {
				categories.value = response.data.categories;
			});
		});

		return {
			categories,
		};
	},
});
</script>

<style lang="less" scoped>
.category-box {
	box-shadow: 0 1px 1px 0 rgb(0 0 0 / 16%);
}

.category {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	width: 100%;
	margin: 20px 0;
}
</style>

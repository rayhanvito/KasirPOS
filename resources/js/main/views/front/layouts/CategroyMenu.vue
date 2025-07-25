<template>
	<template v-for="category in categories" :key="category.xid">
		<a-sub-menu
			v-if="category.children && category.children.length"
			:key="category.xid"
			:title="category.name"
		>
			<template #icon>
				<a-avatar :size="16" :src="category.image_url" />
			</template>

			<category-menu :categories="category.children" />
		</a-sub-menu>
		<a-menu-item v-else :key="category.xid">
			<router-link
				:to="{
					name: 'front.categories',
					params: { warehouse: frontWarehouse.slug, slug: [category.slug] },
				}"
			>
				<a-avatar :size="16" :src="category.image_url" />
				{{ category.name }}
			</router-link>
		</a-menu-item>
	</template>
</template>

<script>
import { defineComponent, ref } from "vue";
import common from "../../../../common/composable/common";

export default defineComponent({
	name: "category-menu",
	props: ["categories"],
	setup() {
		const { frontWarehouse } = common();

		return {
			frontWarehouse,
		};
	},
});
</script>

<style></style>

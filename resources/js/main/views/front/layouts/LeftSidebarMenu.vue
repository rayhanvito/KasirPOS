<template>
	<a-menu
		v-model:selectedKeys="selectedKeys"
		v-model:openKeys="openKeys"
		style="width: 100%"
		mode="inline"
	>
		<template v-for="category in allCategories" :key="category.xid">
			<a-sub-menu
				v-if="category.children && category.children.length"
				:key="category.xid"
			>
				<template #icon>
					<a-avatar :size="16" :src="category.image_url" />
				</template>
				<template #title>{{ category.name }}</template>
				<CategoryMenu :categories="category.children" />
			</a-sub-menu>
		</template>
	</a-menu>
</template>
<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import {
	MailOutlined,
	CalendarOutlined,
	AppstoreOutlined,
	SettingOutlined,
} from "@ant-design/icons-vue";
import { sortBy } from "lodash-es";
import CategoryMenu from "./CategroyMenu.vue";

export default defineComponent({
	props: ["catSelectedKeys", "catOpenKeys"],
	components: {
		MailOutlined,
		CalendarOutlined,
		AppstoreOutlined,
		SettingOutlined,
		CategoryMenu,
	},
	setup(props) {
		const allCategories = ref([]);
		const selectedKeys = ref([]);
		const openKeys = ref([]);

		onMounted(() => {
			selectedKeys.value = props.catSelectedKeys;
			getCategories();
		});

		const getCategories = () => {
			axiosFront.post("front/categories").then((response) => {
				const allCategoriesArray = [];
				var listArray = response.data.categories;
				// listArray = sortBy(listArray, "x_parent_id");

				listArray.forEach((node) => {
					// No parentId means top level
					if (!node.x_parent_id) return allCategoriesArray.push(node);

					// Insert node as child of parent in listArray array
					const parentIndex = listArray.findIndex(
						(el) => el.xid === node.x_parent_id
					);
					if (!listArray[parentIndex].children) {
						return (listArray[parentIndex].children = [node]);
					}

					listArray[parentIndex].children.push(node);
				});

				allCategories.value = allCategoriesArray;
			});
		};

		const getPath = (model, id) => {
			var path,
				item = model.id;

			if (!model || typeof model !== "object") return;

			if (model.id === id) return [item];

			(model.children || []).some((child) => (path = getPath(child, id)));
			return path && [item].concat([...path]);
		};

		watch(props, (newVal, oldVal) => {
			selectedKeys.value = newVal.catSelectedKeys;

			let parentIds = [];
			allCategories.value.forEach((nodeItem) => {
				const result = getPath(nodeItem, selectedKeys.value[0]);
				if (result != undefined && result.includes(selectedKeys.value[0])) {
					parentIds = result;
				}
			});

			openKeys.value = parentIds;
		});

		return {
			selectedKeys,
			openKeys,
			allCategories,
		};
	},
});
</script>

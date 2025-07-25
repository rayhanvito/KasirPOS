<template>
	<div class="state-div">
		<div class="state-icon mr-20">
			<a-avatar :size="46" :style="{ backgroundColor: color ? color : '#1890ff' }">
				<template #icon>
					<slot name="icon"></slot>
				</template>
			</a-avatar>
		</div>
		<div class="state-description">
			<p><slot name="title"></slot></p>
			<span><slot name="description"></slot></span>
		</div>
	</div>
</template>

<script>
import { defineComponent, ref } from "vue";

export default defineComponent({
	props: ["color"],
	setup() {
		return {};
	},
});
</script>

<style lang="less" scoped>
.state-div {
	display: flex;
	align-items: center;
	border: 1px solid #e5e7eb;
	padding: 10px;
	border-radius: 10px;

	.state-icon {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.state-description {
		p {
			margin-bottom: 0px;
			font-size: 15px;
		}
		span {
			font-weight: bold;
			font-size: 18px;
		}
	}
}
</style>

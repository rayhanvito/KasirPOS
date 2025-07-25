<template>
    <MenuOutlined
        v-if="innerWidth < 768"
        :style="{
            fontSize: '24px',
            color: '#fff',
            verticalAlign: 'top',
            paddingTop: '22px',
        }"
        class="mr-20"
        @click="openLeftSidebar"
    />
    <a-drawer
        v-model:open="visible"
        width="300"
        placement="left"
        :closable="false"
        :headerStyle="{ backgroundColor: '#2874f0' }"
        :bodyStyle="{ padding: 0 }"
        @close="drawerClosed"
    >
        <template #title>
            <img style="width: 120px" :src="frontWarehouse.dark_logo_url" />
        </template>
        <template #extra>
            <a-button type="link" @click="closeLeftSidebar" class="close-icon-button">
                <close-outlined :style="{ fontSize: '14px', color: '#fff' }" />
            </a-button>
        </template>

        <!-- <a-typography-title class="pl-24 category-menu-title" :level="5">
            {{ $t("front.categories") }}
        </a-typography-title>
        <LeftSidebarMenu :catSelectedKeys="[]" /> -->

        <div
            v-if="
                frontAppSetting &&
                frontAppSetting.pages_widget &&
                frontAppSetting.pages_widget.length > 0
            "
        >
            <a-typography-title class="pl-24 mt-30 category-menu-title" :level="5">
                {{ $t("front.pages") }}
            </a-typography-title>

            <a-menu
                v-model:openKeys="openKeys"
                v-model:selectedKeys="selectedKeys"
                style="width: 100%"
                mode="inline"
            >
                <a-menu-item
                    v-for="(item, index) in frontAppSetting.pages_widget"
                    :key="index"
                >
                    <router-link :to="item.value">
                        {{ item.title }}
                    </router-link>
                </a-menu-item>
            </a-menu>
        </div>
    </a-drawer>
</template>

<script>
import { defineComponent, reactive, toRefs, ref, computed } from "vue";
import {
    MenuOutlined,
    CloseOutlined,
    ShoppingOutlined,
    RightOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import LeftSidebarMenu from "./LeftSidebarMenu.vue";

export default defineComponent({
    components: {
        MenuOutlined,
        CloseOutlined,
        ShoppingOutlined,
        RightOutlined,
        LeftSidebarMenu,
    },
    setup() {
        const { frontWarehouse, frontAppSetting } = common();
        const visible = ref(false);
        const state = reactive({
            selectedKeys: [],
            openKeys: [],
        });

        const openLeftSidebar = () => {
            visible.value = true;
        };

        const closeLeftSidebar = () => {
            visible.value = false;
        };

        const drawerClosed = () => {
            state.selectedKeys = [];
            state.openKeys = [];
        };

        return {
            ...toRefs(state),
            frontWarehouse,
            frontAppSetting,
            visible,
            openLeftSidebar,
            closeLeftSidebar,
            drawerClosed,

            innerWidth: computed(() => window.innerWidth),
        };
    },
});
</script>

<style lang="less" scoped>
.category-menu-title {
    padding-top: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}

.close-icon-button {
    margin-right: -20px;
}
</style>

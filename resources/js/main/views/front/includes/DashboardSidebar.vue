<template>
    <a-menu
        v-model:selectedKeys="selectedKeys"
        :style="{
            paddingTop: '30px',
            paddingBottom: '30px',
            paddingLeft: '25px',
            paddingRight: '25px',
            borderRadius: '10px',
        }"
    >
        <a-menu-item key="dashboard">
            <template #icon>
                <home-outlined />
            </template>
            <router-link :to="{ name: 'front.dashboard' }">
                {{ $t("front.dashboard") }}
            </router-link>
        </a-menu-item>

        <a-menu-item key="orders">
            <template #icon>
                <shopping-outlined />
            </template>
            <router-link :to="{ name: 'front.orders' }">
                {{ $t("front.my_orders") }}
            </router-link>
        </a-menu-item>

        <a-menu-item key="profile">
            <template #icon>
                <setting-outlined />
            </template>
            <router-link :to="{ name: 'front.profile' }">
                {{ $t("front.my_profile") }}
            </router-link>
        </a-menu-item>
        <a-menu-item @click="logout" key="logout">
            <template #icon>
                <logout-outlined />
            </template>
            {{ $t("front.logout") }}
        </a-menu-item>
    </a-menu>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    HomeOutlined,
    ShoppingOutlined,
    SettingOutlined,
    LogoutOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

export default defineComponent({
    components: {
        HomeOutlined,
        ShoppingOutlined,
        SettingOutlined,
        LogoutOutlined,
    },
    setup() {
        const route = useRoute();
        const store = useStore();
        const selectedKeys = ref([]);

        onMounted(() => {
            const menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        const logout = () => {
            store.dispatch("front/logout");
        };

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        return {
            selectedKeys,
            logout,
        };
    },
});
</script>

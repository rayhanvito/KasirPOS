<template>
    <a-dropdown
        v-if="user && user.user_type && user.user_type == 'staff_members'"
        :placement="appSetting.rtl ? 'bottomLeft' : 'bottomRight'"
    >
        <a class="ant-dropdown-link" @click.prevent>
            {{
                selectedWarehouse && selectedWarehouse.name ? selectedWarehouse.name : ""
            }}
            <DownOutlined />
        </a>
        <template #overlay>
            <a-menu>
                <a-menu-item
                    v-for="warehouse in allWarehouses"
                    :key="warehouse.xid"
                    @click="warehouseChanged(warehouse.xid)"
                >
                    {{ warehouse.name }}
                </a-menu-item>
            </a-menu>
        </template>
    </a-dropdown>
    <span v-else>
        {{ selectedWarehouse && selectedWarehouse.name ? selectedWarehouse.name : "" }}
    </span>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";
import { DownOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import common from "../../common/composable/common";

export default defineComponent({
    components: {
        DownOutlined,
    },
    setup() {
        const store = useStore();
        const {
            selectedWarehouse,
            allWarehouses,
            appSetting,
            permsArray,
            user,
        } = common();

        const warehouseChanged = (selectedWarehouseId) => {
            axiosAdmin
                .post("change-warehouse", { warehouse_id: selectedWarehouseId })
                .then((response) => {
                    store.commit("auth/updateWarehouse", response.data.warehouse);
                });
        };

        return {
            allWarehouses,
            selectedWarehouse,
            warehouseChanged,
            appSetting,
            permsArray,
            user,
        };
    },
});
</script>

<style></style>

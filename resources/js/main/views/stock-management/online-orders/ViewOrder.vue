<template>
    <a-modal
        :open="visible"
        :centered="true"
        :title="$t('online_orders.update_orders_status')"
        @cancel="onClose"
        width="70%"
        :footer="false"
    >
        <OrderDetails :order="order" detailView="admin" />
    </a-modal>
</template>
<script>
import { defineComponent } from "vue";
import OrderDetails from "../../front/includes/OrderDetails.vue";

export default defineComponent({
    props: ["order", "visible"],
    emits: ["closed"],
    components: {
        OrderDetails,
    },
    setup(props, { emit }) {
        const onClose = () => {
            emit("closed");
        };

        return {
            onClose,
        };
    },
});
</script>

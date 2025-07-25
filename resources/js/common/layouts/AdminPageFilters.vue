<template>
    <div :style="{ padding: '10px 16px', background: 'white' }" class="mb-20">
        <slot></slot>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    setup() {
        return {};
    },
});
</script>

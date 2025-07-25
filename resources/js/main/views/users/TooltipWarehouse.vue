<template>
    <a-tooltip>
        <template #title>
            {{ $t("user.warehouse_not_changable") }}
        </template>
        <InfoCircleOutlined />
    </a-tooltip>
</template>
<script>
import { InfoCircleOutlined } from "@ant-design/icons-vue";

export default {
    components: {
        InfoCircleOutlined,
    },
};
</script>

<template>
    <a-col :span="24">
        <a-button type="primary" v-print="`#${tableName}`">
            <PrinterOutlined />
            {{ $t("common.print") }}
        </a-button>
    </a-col>
</template>

<script>
import { PrinterOutlined } from "@ant-design/icons-vue";

export default {
    props: ["tableName"],
    components: {
        PrinterOutlined,
    },
};
</script>

<style></style>

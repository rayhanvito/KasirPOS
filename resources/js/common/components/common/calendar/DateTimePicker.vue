<template>
    <a-date-picker
        v-model:value="dateTimeValue"
        :format="formatOrderDate"
        :disabled-date="disabledDate"
        show-time
        :placeholder="$t('common.date_time')"
        style="width: 100%"
        @change="dateTimeChanged"
        :disabled="disabled"
    />
</template>

<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import common from "../../../composable/common";

export default defineComponent({
    props: {
        dateTime: {
            default: undefined,
        },
        disabled: {
            default: false,
        },
    },
    emits: ["dateTimeChanged"],
    setup(props, { emit }) {
        const dateTimeValue = ref(undefined);
        const { disabledDate, formatDateTime, dayjs } = common();

        onMounted(() => {
            setDateTime(props.dateTime);
        });

        const setDateTime = (setValue) => {
            if (props.dateTime) {
                dateTimeValue.value = dayjs(setValue);
            } else {
                dateTimeValue.value = undefined;
            }
        };

        const formatOrderDate = (newValue) => {
            return newValue ? formatDateTime(newValue.format()) : undefined;
        };

        const dateTimeChanged = (newValue) => {
            const emitValue = newValue
                ? newValue.utc().format("YYYY-MM-DDTHH:mm:ssZ")
                : undefined;
            emit("dateTimeChanged", emitValue);
        };

        watch(
            () => props.dateTime,
            (newVal, oldVal) => {
                setDateTime(newVal);
            }
        );

        return {
            dateTimeValue,
            disabledDate,
            formatOrderDate,
            dateTimeChanged,
        };
    },
});
</script>

<style></style>

<template>
    <a-card :bordered="false">
        <figure class="custom-card-widget">
            <a-space>
                <div class="custom-card-widget-icon" :style="{ background: bgColor }">
                    <slot name="image"></slot>
                </div>
                <figcaption>
                    <slot name="description"></slot>
                </figcaption>
            </a-space>
        </figure>
    </a-card>
</template>

<script>
export default {
    props: {
        bgColor: {
            default: "#5F63F2",
        },
    },
};
</script>

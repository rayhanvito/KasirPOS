<template>
    <a-card :bordered="false">
        <figure class="custom-card-widget">
            <a-space :size="[8, 0]" direction="vertical" align="center">
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

<style lang="less" scoped>
.custom-card-widget {
    height: 90px; /* Tinggi yang lebih ramping */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.custom-card-widget-icon {
    width: 40px; /* Ukuran ikon yang lebih kecil */
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px; /* Jarak yang lebih kecil antara ikon dan teks */

    :deep(.anticon) {
        font-size: 18px; /* Ukuran font ikon yang lebih kecil */
    }
}

figcaption {
    h2 {
        font-size: 18px; /* Ukuran font judul yang lebih kecil */
        margin-bottom: 2px; /* Jarak yang lebih kecil */
    }
    p {
        font-size: 12px; /* Ukuran font deskripsi yang lebih kecil */
        margin-bottom: 0;
    }
}
</style>
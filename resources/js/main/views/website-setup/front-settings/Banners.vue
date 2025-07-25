<template>
    <a-card
        :title="$t('front_setting.banners')"
        class="page-content-container mt-20 mb-20"
    >
        <a-form layout="vertical" class="mb-20">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.top_banners_1") }}
                    </a-typography-title>
                    <FileUploader
                        key="bottom_banners_1"
                        :fileUrls="data.bottom_banners_1_details"
                        :fileNames="formData.bottom_banners_1"
                        @uploadSuccess="bottomBanner1UploadSuccess"
                    />
                </a-col>
            </a-row>
            <a-divider />

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.top_banners_2") }}
                    </a-typography-title>
                    <FileUploader
                        key="bottom_banners_2"
                        :fileUrls="data.bottom_banners_2_details"
                        :fileNames="formData.bottom_banners_2"
                        @uploadSuccess="bottomBanner2UploadSuccess"
                    />
                </a-col>
            </a-row>
            <a-divider />

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.top_banners_3") }}
                    </a-typography-title>
                    <FileUploader
                        key="bottom_banners_3"
                        :fileUrls="data.bottom_banners_3_details"
                        :fileNames="formData.bottom_banners_3"
                        @uploadSuccess="bottomBanner3UploadSuccess"
                    />
                </a-col>
            </a-row>
            <a-divider />

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-typography-title :level="5" :style="{ marginBottom: '20px' }">
                        {{ $t("front_setting.bottom_banners") }}
                    </a-typography-title>
                    <FileUploader
                        key="top_banners_details"
                        :fileUrls="data.top_banners_details"
                        :fileNames="formData.top_banners"
                        @uploadSuccess="topBannerUploadSuccess"
                    />
                </a-col>
            </a-row>
        </a-form>
    </a-card>
</template>

<script>
import { defineComponent, reactive, ref, onMounted, watch } from "vue";
import {
    SaveOutlined,
    SearchOutlined,
    DeleteOutlined,
    PlusOutlined,
    MinusCircleOutlined,
} from "@ant-design/icons-vue";
import DyanmicForm from "./DyanmicForm.vue";
import FileUploader from "./FileUploader.vue";

export default defineComponent({
    props: ["formData", "data", "rules"],
    emits: ["onSubmit"],
    components: {
        SaveOutlined,
        SearchOutlined,
        DeleteOutlined,
        PlusOutlined,
        MinusCircleOutlined,
        DyanmicForm,
        FileUploader,
    },
    setup(props, { emit }) {
        const addEditForm = reactive({
            formSubmitting: false,
            formData: props.formData,
        });

        onMounted(() => {
            addEditForm.formData = props.formData;
        });

        const updateContactInfoWidget = (resultArray) => {
            addEditForm.formData.contact_info_widget = resultArray;
        };

        const onSubmit = () => {
            emit("onSubmit", addEditForm.formData);
        };

        const topBannerUploadSuccess = (uploadData) => {
            addEditForm.formData.top_banners = uploadData;
            onSubmit();
        };

        const bottomBanner1UploadSuccess = (uploadData) => {
            addEditForm.formData.bottom_banners_1 = uploadData;
            onSubmit();
        };

        const bottomBanner2UploadSuccess = (uploadData) => {
            addEditForm.formData.bottom_banners_2 = uploadData;
            onSubmit();
        };

        const bottomBanner3UploadSuccess = (uploadData) => {
            addEditForm.formData.bottom_banners_3 = uploadData;
            onSubmit();
        };

        return {
            addEditForm,
            onSubmit,
            updateContactInfoWidget,
            topBannerUploadSuccess,
            bottomBanner1UploadSuccess,
            bottomBanner2UploadSuccess,
            bottomBanner3UploadSuccess,
        };
    },
});
</script>

<style></style>

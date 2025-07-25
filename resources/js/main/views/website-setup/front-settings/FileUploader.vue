<template>
    <div class="clearfix">
        <a-upload
            accept="image/*"
            name="image"
            v-model:file-list="fileList"
            list-type="picture-card"
            :show-upload-list="true"
            @preview="handlePreview"
            :customRequest="customRequest"
            :remove="handleRemove"
        >
            <div v-if="loading">
                <loading-outlined></loading-outlined>
            </div>
            <div v-else>
                <plus-outlined />
                <div class="ant-upload-text">Upload</div>
            </div>
        </a-upload>
        <a-modal :open="previewVisible" :footer="null" @cancel="handleCancel">
            <img alt="example" style="width: 100%" :src="previewImage" />
        </a-modal>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted } from "vue";
import { PlusOutlined, LoadingOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import { filter, forEach } from "lodash-es";

function getBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = () => resolve(reader.result);

        reader.onerror = (error) => reject(error);
    });
}

export default defineComponent({
    props: ["fileNames", "fileUrls"],
    emits: ["uploadSuccess"],
    components: {
        PlusOutlined,
        LoadingOutlined,
    },
    setup(props, { emit }) {
        const previewVisible = ref(false);
        const previewImage = ref("");
        const fileList = ref([]);
        const fileArray = ref([]);
        const fileArrayWithUid = ref([]);
        const loading = ref(false);

        onMounted(() => {
            fileList.value = [...props.fileUrls];
            fileArray.value = [...props.fileNames];
            fileArrayWithUid.value = [...props.fileUrls];
        });

        const handleCancel = () => {
            previewVisible.value = false;
        };

        const handlePreview = async (file) => {
            if (!file.url && !file.preview) {
                file.preview = await getBase64(file.originFileObj);
            }

            previewImage.value = file.url || file.preview;
            previewVisible.value = true;
        };

        const customRequest = ({ onSuccess, onError, file }) => {
            const formData = new FormData();
            formData.append("image", file);
            formData.append("folder", "banners");
            loading.value = true;

            axiosAdmin
                .post("upload-file", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    fileArray.value.push(response.data.file);

                    loading.value = false;
                    onSuccess({ name: response.data.file }, file);

                    fileArrayWithUid.value.push({
                        uid: file.uid,
                        name: response.data.file,
                    });

                    emit("uploadSuccess", fileArray.value);
                })
                .catch(() => {
                    loading.value = false;
                    message.error("upload failed.");
                });
        };

        const handleRemove = (file) => {
            const newFileArray = filter(
                fileArrayWithUid.value,
                (result) => result.uid != file.uid
            );

            const fileArrayResult = [];
            forEach(newFileArray, (reslutValue) => {
                fileArrayResult.push(reslutValue.name);
            });

            fileArrayWithUid.value = newFileArray;
            fileArray.value = fileArrayResult;

            emit("uploadSuccess", fileArray.value);
        };

        return {
            previewVisible,
            previewImage,
            fileList,
            handleCancel,
            handlePreview,

            loading,
            customRequest,
            fileArray,
            handleRemove,
        };
    },
});
</script>
<style>
/* you can make up upload button and sample style by using stylesheets */
.ant-upload-select-picture-card i {
    font-size: 32px;
    color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
    margin-top: 8px;
    color: #666;
}
</style>

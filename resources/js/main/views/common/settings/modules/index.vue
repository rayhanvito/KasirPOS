<template>
    <a-row v-if="offers && offers.length > 0">
        <a-col :span="24">
            <a-carousel arrows>
                <template #prevArrow>
                    <div class="custom-slick-arrow" style="left: 10px; z-index: 1">
                        <left-circle-outlined />
                    </div>
                </template>
                <template #nextArrow>
                    <div class="custom-slick-arrow" style="right: 10px">
                        <right-circle-outlined />
                    </div>
                </template>
                <div v-for="offer in offers" :key="offer.id">
                    <a-typography-link :href="offer.url" target="_blank">
                        <img
                            :src="offer.image_url"
                            :style="{
                                width: settings.width,
                                height: settings.height,
                            }"
                            :preview="false"
                        />
                    </a-typography-link>
                </div>
            </a-carousel>
        </a-col>
    </a-row>

    <admin-page-table-content>
        <a-row class="mt-20 mb-20">
            <a-col :span="24">
                <div class="table-responsive" v-if="allModules">
                    <a-table
                        :columns="modulesTableColumns"
                        :row-key="(record) => record.verified_name"
                        :data-source="allModules"
                        :pagination="false"
                        bordered
                        size="middle"
                        :loading="dataLoading"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'name'">
                                <a-list>
                                    <a-list-item>
                                        <a-list-item-meta
                                            :description="record.description"
                                        >
                                            <template #title>
                                                {{ record.name }}
                                            </template>
                                            <template #avatar>
                                                <a-avatar :src="record.image_url" />
                                            </template>
                                        </a-list-item-meta>
                                    </a-list-item>
                                </a-list>
                            </template>
                            <template v-if="column.dataIndex == 'verified'">
                                <VerifyModule
                                    :module="record"
                                    @verifySuccess="getModuleData"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'status'">
                                <ModuleStatus
                                    :module="record"
                                    :status="record.status"
                                    :moduleName="record.verified_name"
                                    @updateSuccess="getModuleData"
                                />
                            </template>
                            <template v-if="column.dataIndex == 'action'">
                                <ModuleAction
                                    v-if="appEnv == 'envato'"
                                    :module="record"
                                    @install="installModule"
                                    :downloadPercentage="downloadPercentage"
                                    :downloading="downloading"
                                    :extracting="extracting"
                                />
                                <span v-else>-</span>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";
import { LeftCircleOutlined, RightCircleOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import modules from "../../../../../common/composable/modules";
import ModuleStatus from "./ModuleStatus.vue";
import VerifyModule from "./VerifyModule.vue";
import ModuleAction from "./ModuleAction.vue";

export default defineComponent({
    components: {
        ModuleStatus,
        VerifyModule,
        ModuleAction,
        LeftCircleOutlined,
        RightCircleOutlined,
    },
    setup() {
        const { t } = useI18n();
        const store = useStore();
        const appEnv = window.config.app_env;

        const {
            allModules,
            dataLoading,
            getModuleData,
            offers,
            settings,
            install,
            downloading,
            downloadPercentage,
            extracting,
        } = modules();

        const modulesTableColumns = [
            {
                title: t("module.name"),
                dataIndex: "name",
                width: "25%",
            },
            {
                title: t("module.verified"),
                dataIndex: "verified",
                width: "15%",
            },
            {
                title: t("module.current_version"),
                dataIndex: "current_version",
            },
            {
                title: t("module.latest_version"),
                dataIndex: "version",
            },
            {
                title: t("module.status"),
                dataIndex: "status",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ];

        onMounted(() => {
            getModuleData();
        });

        const installModule = (moduleName) => {
            install(moduleName);
        };

        return {
            modulesTableColumns,
            allModules,
            getModuleData,
            installModule,
            dataLoading,

            offers,
            settings,
            downloading,
            downloadPercentage,
            extracting,
            appEnv,
        };
    },
});
</script>

<style lang="less">
.module-description {
    max-width: 300px;
    word-wrap: break-word; /* IE 5.5-7 */
    white-space: -moz-pre-wrap; /* Firefox 1.0-2.0 */
    white-space: pre-wrap; /* current browsers */
}
</style>

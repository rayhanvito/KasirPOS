<template>
    <div class="mt-30 mb-30">
        <a-row type="flex" justify="center">
            <a-col :span="20">
                <a-breadcrumb>
                    <a-breadcrumb-item>
                        <router-link :to="{ name: 'front.homepage' }">
                            {{ $t("front.home") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>
                        <router-link :to="{ name: 'front.dashboard' }">
                            {{ $t("front.dashboard") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>{{ $t("front.my_orders") }}</a-breadcrumb-item>
                </a-breadcrumb>

                <a-row :gutter="[30, 30]" class="mt-30">
                    <a-col :xs="24" :sm="24" :md="24" :lg="6" :xl="6">
                        <dashboard-sidebar />
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
                        <a-card
                            :title="null"
                            :bordered="false"
                            :style="{ borderRadius: '10px' }"
                            class="dashboard-container"
                        >
                            <a-row>
                                <a-col :span="24">
                                    <a-page-header
                                        :title="$t('front.order_history')"
                                        style="padding-left: 0px"
                                    >
                                        <template #extra>
                                            <a-range-picker
                                                v-model:value="filters.dates"
                                                :placeholder="[
                                                    $t('common.start_date'),
                                                    $t('common.end_date'),
                                                ]"
                                                style="width: 100%"
                                                @change="fetchOrders"
                                                :valueFormat="dateFormat"
                                            />
                                        </template>
                                    </a-page-header>
                                </a-col>
                            </a-row>
                            <a-row>
                                <a-col :span="24">
                                    <a-tabs
                                        v-model:activeKey="filters.order_status_type"
                                        @change="fetchOrders"
                                    >
                                        <a-tab-pane key="all" tab="All Orders" />
                                        <a-tab-pane
                                            v-for="status in orderStatus"
                                            :key="status.key"
                                            :tab="`${status.value}`"
                                        />
                                    </a-tabs>

                                    <div class="table-responsive">
                                        <OrderTable
                                            :data="myOrders"
                                            @reloadOrders="fetchOrders"
                                        />
                                    </div>
                                </a-col>
                            </a-row>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { onMounted, watch, ref, reactive, createVNode } from "vue";
import apiFront from "../../../../common/composable/apiFront";
import common from "../../../../common/composable/common";
import DashboardSidebar from "../includes/DashboardSidebar.vue";
import OrderTable from "../components/OrderTable.vue";

export default {
    components: {
        DashboardSidebar,
        OrderTable,
    },
    setup() {
        const { addEditRequestFront } = apiFront();
        const { orderStatus } = common();
        const myOrders = ref([]);

        const filters = reactive({
            order_status_type: "all",
            dates: [],
        });
        const dateFormat = "YYYY-MM-DD";

        onMounted(() => {
            fetchOrders();
        });

        const fetchOrders = () => {
            const urlString = "front/self/orders";

            addEditRequestFront({
                url: urlString,
                data: filters,
                success: (response) => {
                    myOrders.value = response.orders;
                },
            });
        };

        return {
            myOrders,

            filters,
            orderStatus,
            fetchOrders,
            dateFormat,
        };
    },
};
</script>

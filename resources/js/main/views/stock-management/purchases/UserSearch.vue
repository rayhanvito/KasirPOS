<template>
    <a-form-item
        :label="$t(`${orderPageObject.langKey}.user`)"
        name="user_id"
        :help="rules.user_id ? rules.user_id.message : null"
        :validateStatus="rules.user_id ? 'error' : null"
        class="required"
    >
        <span style="display: flex">
            <a-select
                v-model:value="orderSearchTerm"
                show-search
                :filter-option="false"
                :placeholder="
                    $t('common.select_default_text', [
                        $t(`${orderPageObject.langKey}.user`),
                    ])
                "
                style="width: 100%"
                :not-found-content="productFetching ? undefined : null"
                @search="fetchProducts"
                option-label-prop="label"
                @select="searchValueSelected"
                :disabled="editOrderDisable"
            >
                <template #suffixIcon><SearchOutlined /></template>
                <template v-if="productFetching" #notFoundContent>
                    <a-spin size="small" />
                </template>
                <a-select-option
                    v-for="user in products"
                    :key="user.xid"
                    :value="user.xid"
                    :label="user.name"
                    :product="user"
                >
                    {{ user.name }}
                    <span v-if="user.phone && user.phone != ''">
                        <br />
                        {{ user.phone }}
                    </span>
                </a-select-option>
            </a-select>
            <SupplierAddButton v-if="orderPageObject.userType == 'suppliers'" />
            <CustomerAddButton v-else />
        </span>
    </a-form-item>
</template>

<script>
import { defineComponent, watch, ref, toRefs, reactive } from "vue";
import { SearchOutlined } from "@ant-design/icons-vue";
import { debounce } from "lodash-es";
import SupplierAddButton from "../../users/SupplierAddButton.vue";
import CustomerAddButton from "../../users/CustomerAddButton.vue";

export default defineComponent({
    props: ["orderPageObject", "rules", "usersList", "editOrderDisable"],
    emits: ["onSuccess"],
    components: {
        SearchOutlined,

        SupplierAddButton,
        CustomerAddButton,
    },
    setup(props, { emit }) {
        const state = reactive({
            orderSearchTerm: [],
            productFetching: false,
            products: [],
        });

        const fetchProducts = debounce((value) => {
            state.products = [];

            if (value != "") {
                const newValue = value.trim();
                state.productFetching = true;
                const filterString = `name lk "${newValue}%" or (phone lk "${newValue}%")`;
                let url = `${
                    props.orderPageObject.userType
                }?fields=id,xid,name,phone&filters=${encodeURIComponent(filterString)}`;

                axiosAdmin.get(url).then((response) => {
                    state.products = response.data;
                    state.productFetching = false;
                });
            }
        }, 300);

        const searchValueSelected = (value, option) => {
            emit("onSuccess", value);
        };

        watch(
            () => props.usersList,
            (newVal, oldVal) => {
                state.products = [newVal];
                state.orderSearchTerm = newVal.xid;
            }
        );

        return {
            ...toRefs(state),
            fetchProducts,
            searchValueSelected,
        };
    },
});
</script>

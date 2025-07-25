<template>
    <a-row
        :gutter="16"
        v-if="formData.product_type == 'variable'"
        style="margin-bottom: 20px"
    >
        <a-col :xs="24" :sm="24" :md="6" :lg="6">
            <a-select
                v-model:value="variantId"
                :placeholder="
                    $t('common.select_default_text', [$t('variations.variation')])
                "
                @change="getValueByVariation"
                :style="{ width: '100%' }"
                :allowClear="false"
                mode="multiple"
            >
                <a-select-option
                    v-for="varaitionType in varaitionTypes"
                    :key="varaitionType.xid"
                    :value="varaitionType.xid"
                >
                    {{ varaitionType.name }}
                </a-select-option>
            </a-select>
        </a-col>
        <a-col :xs="24" :sm="24" :md="6" :lg="6">
            <a-select
                :value="variantValueId"
                :placeholder="
                    $t('common.select_default_text', [$t('variations.variant_value')])
                "
                :allowClear="false"
                :style="{ width: '100%' }"
                :disabled="variantId == undefined"
                :options="varaitionTypeValues"
                mode="multiple"
                :field-names="{
                    label: 'label',
                    value: 'value',
                    parentId: 'parentId',
                    options: 'children',
                }"
                @select="variantValueSelected"
                @deselect="variantValueDeselected"
            />
        </a-col>
        <a-col :xs="24" :sm="24" :md="6" :lg="6">
            <a-button
                type="primary"
                @click="addNewVariant"
                :disabled="variantValueId.length <= 0"
            >
                {{ $t("variations.add") }}
                <template #icon> <PlusOutlined /> </template>
            </a-button>
        </a-col>
    </a-row>

    <a-table
        v-if="formData.product_type == 'variable'"
        :dataSource="variantTableData"
        :columns="variantTableColumns"
        :row-key="
            (record) =>
                record.xid && record.xid != '' ? record.xid : generateRandomTableKey()
        "
        size="middle"
        :pagination="false"
        class="mb-20"
    >
        <template #bodyCell="{ column, text, record }">
            <template v-if="column.dataIndex === 'image'">
                <a-badge>
                    <a-avatar shape="square" :src="record.image_url" />
                </a-badge>
            </template>
            <template v-if="column.dataIndex === 'variant_id'">
                <ul>
                    <li v-for="multiVariantId in record.variant_id" :key="multiVariantId">
                        {{ getVariantNameById(multiVariantId) }}
                    </li>
                </ul>
            </template>
            <template v-if="column.dataIndex === 'variant_value_id'">
                <ul>
                    <li
                        v-for="multiVariantValueId in record.variant_value_id"
                        :key="multiVariantValueId"
                    >
                        {{ getVariantNameById(multiVariantValueId) }}
                    </li>
                </ul>
            </template>
            <template
                v-if="
                    column.dataIndex === 'purchase_price' &&
                    record &&
                    record.purchase_price
                "
            >
                {{ formatAmountCurrency(record.purchase_price) }}
            </template>

            <template
                v-if="column.dataIndex === 'sales_price' && record && record.sales_price"
            >
                {{ formatAmountCurrency(record.sales_price) }}
            </template>
            <template v-if="column.dataIndex === 'mrp' && record && record.mrp">
                {{ formatAmountCurrency(record.mrp) }}
            </template>
            <template v-if="column.dataIndex === 'action'">
                <a-button type="primary" @click="editItem(record)">
                    <template #icon><EditOutlined /></template>
                </a-button>
            </template>
        </template>
    </a-table>

    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="addEditType == 'add' ? $t('variations.add') : $t('variations.edit')"
        @ok="onSubmit"
        :width="modalWidth"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item :label="$t('product.image')" name="image">
                        <Upload
                            :formData="addEditForm"
                            folder="product"
                            @onFileUploaded="
                                (file) => {
                                    addEditForm.image = file.file;
                                    addEditForm.image_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="16" :lg="16">
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('product.item_code')"
                                name="item_code"
                                :help="rules.item_code ? rules.item_code.message : null"
                                :validateStatus="rules.item_code ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="addEditForm.item_code"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.item_code'),
                                        ])
                                    "
                                >
                                    <template #addonAfter>
                                        <a-button
                                            v-if="addEditForm.item_code == ''"
                                            type="text"
                                            size="small"
                                            @click="
                                                () => {
                                                    addEditForm.item_code = parseInt(
                                                        Math.random() * 10000000000
                                                    );
                                                }
                                            "
                                        >
                                            <template #icon>
                                                <BarcodeOutlined />
                                            </template>
                                            {{ $t("product.generate_barcode") }}
                                        </a-button>
                                        <Barcode
                                            :itemCode="addEditForm.item_code"
                                            :barcodeSymbology="formData.barcode_symbology"
                                            v-else
                                        />
                                    </template>
                                </a-input>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item name="stock_quantitiy_alert">
                                <template #label>
                                    <InputLabelPopover
                                        :label="$t('product.quantitiy_alert')"
                                        :content="$t('popover.quantitiy_alert')"
                                    />
                                </template>
                                <a-input-number
                                    v-model:value="addEditForm.stock_quantitiy_alert"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.quantitiy_alert'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item :label="$t('product.mrp')" name="mrp">
                                <a-input-number
                                    v-model:value="addEditForm.mrp"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.mrp'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                >
                                    <template #addonBefore>
                                        {{ appSetting.currency.symbol }}
                                    </template>
                                </a-input-number>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.purchase_price')"
                        name="purchase_price"
                        :help="rules.purchase_price ? rules.purchase_price.message : null"
                        :validateStatus="rules.purchase_price ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="addEditForm.purchase_price"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.purchase_price'),
                                ])
                            "
                            min="0"
                            style="width: 100%"
                        >
                            <template #addonBefore>
                                {{ appSetting.currency.symbol }}
                            </template>
                            <template #addonAfter>
                                <a-select
                                    v-model:value="addEditForm.purchase_tax_type"
                                    style="width: 100px"
                                >
                                    <a-select-option value="inclusive">
                                        {{ $t("common.with_tax") }}
                                    </a-select-option>
                                    <a-select-option value="exclusive">
                                        {{ $t("common.without_tax") }}
                                    </a-select-option>
                                </a-select>
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.sales_price')"
                        name="sales_price"
                        :help="rules.sales_price ? rules.sales_price.message : null"
                        :validateStatus="rules.sales_price ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="addEditForm.sales_price"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.sales_price'),
                                ])
                            "
                            min="0"
                            style="width: 100%"
                        >
                            <template #addonBefore>
                                {{ appSetting.currency.symbol }}
                            </template>
                            <template #addonAfter>
                                <a-select
                                    v-model:value="addEditForm.sales_tax_type"
                                    style="width: 100px"
                                >
                                    <a-select-option value="inclusive">
                                        {{ $t("common.with_tax") }}
                                    </a-select-option>
                                    <a-select-option value="exclusive">
                                        {{ $t("common.without_tax") }}
                                    </a-select-option>
                                </a-select>
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.opening_stock')"
                        name="opening_stock"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="addEditForm.opening_stock"
                            placeholder="0"
                            style="width: 100%"
                        >
                            <template #addonAfter>
                                {{
                                    selectedUnit && selectedUnit.short_name
                                        ? selectedUnit.short_name
                                        : ""
                                }}
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.opening_stock_date')"
                        name="opening_stock_date"
                    >
                        <a-date-picker
                            v-model:value="addEditForm.opening_stock_date"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, onMounted, watch, computed } from "vue";
import {
    PlusOutlined,
    SaveOutlined,
    EditOutlined,
    BarcodeOutlined,
} from "@ant-design/icons-vue";
import { forEach, find, map, some, includes, filter } from "lodash-es";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import Barcode from "./Barcode.vue";
import InputLabelPopover from "../../../../common/components/common/typography/InputLabelPopover.vue";

export default defineComponent({
    props: ["formData", "variations", "selectedUnit", "showTable"],
    emits: ["variant_added"],
    components: {
        PlusOutlined,
        SaveOutlined,
        EditOutlined,
        BarcodeOutlined,

        Upload,
        Barcode,
        InputLabelPopover,
    },
    setup(props, { emit }) {
        const { appSetting, formatAmountCurrency } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const variantId = ref([]);
        const variantValueId = ref([]);

        const varaitionTypes = ref([]);
        const varaitionTypeValues = ref([]);
        const { t } = useI18n();

        const addEditType = ref("add");
        const visible = ref(false);
        const addEditForm = ref({});

        const variantTableData = ref([]);
        const variantTableColumns = ref([
            {
                title: t("product.image"),
                dataIndex: "image",
            },
            {
                title: t("variations.variation"),
                dataIndex: "variant_id",
            },
            {
                title: t("variations.variant_value"),
                dataIndex: "variant_value_id",
            },
            {
                title: t("product.item_code"),
                dataIndex: "item_code",
            },
            {
                title: t("product.purchase_price"),
                dataIndex: "purchase_price",
            },
            {
                title: t("product.sales_price"),
                dataIndex: "sales_price",
            },
            {
                title: t("product.mrp"),
                dataIndex: "mrp",
            },
            {
                title: t("product.opening_stock"),
                dataIndex: "opening_stock",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ]);

        onMounted(() => {
            getVaraitionTypes();
            setVariantTableData(props.formData.variations);
        });

        const setVariantTableData = (variableTableDataArray) => {
            var newTableData = [];

            forEach(variableTableDataArray, (variableTableDataArr) => {
                var variantIdArray = [];
                var variantTypeIdArray = [];
                forEach(variableTableDataArr.product_variations, (multiVariant) => {
                    variantIdArray.push(multiVariant.x_variant_id);
                    variantTypeIdArray.push(multiVariant.x_variant_value_id);
                });

                newTableData.push({
                    unique_id: generateRandomTableKey(), // Key used in edit time for newly added item
                    image: variableTableDataArr.image,
                    image_url: variableTableDataArr.image_url,
                    variant_id: variantIdArray,
                    variant_value_id: variantTypeIdArray,
                    xid: variableTableDataArr.xid,
                    item_code: variableTableDataArr.item_code,
                    purchase_price: variableTableDataArr.details.purchase_price,
                    purchase_tax_type: variableTableDataArr.details.purchase_tax_type,
                    sales_price: variableTableDataArr.details.sales_price,
                    sales_tax_type: variableTableDataArr.details.sales_tax_type,
                    mrp: variableTableDataArr.details.mrp,
                    opening_stock: variableTableDataArr.details.opening_stock,
                    opening_stock_date: variableTableDataArr.details.opening_stock_date,
                    stock_quantitiy_alert:
                        variableTableDataArr.details.stock_quantitiy_alert,
                });
            });

            variantTableData.value = newTableData;
            emit("variant_added", variantTableData.value);
        };

        const setDirectVariantTableData = (newDataValue) => {
            variantTableData.value = newDataValue;
        };

        const generateRandomTableKey = () => {
            return Math.random().toString(36).slice(2);
        };

        const getVaraitionTypes = () => {
            var allVariations = [];
            forEach(props.variations, (variation) => {
                if (variation.x_parent_id == null) {
                    allVariations.push(variation);
                }
            });

            varaitionTypes.value = allVariations;
        };

        const getValueByVariation = (value, options) => {
            varaitionTypeValues.value = [];

            var allLists = [];
            var newVariantTypesValues = [];
            forEach(props.variations, (variation) => {
                if (
                    includes(value, variation.xid) &&
                    variation.sub_variations.length > 0
                ) {
                    var allChildrens = [];

                    forEach(variation.sub_variations, (subVariation) => {
                        allChildrens.push({
                            label: subVariation.name,
                            value: subVariation.xid,
                            parentId: variation.xid,
                        });

                        if (includes(variantValueId.value, subVariation.xid)) {
                            newVariantTypesValues.push(subVariation.xid);
                        }
                    });

                    allLists.push({
                        label: variation.name,
                        value: variation.xid,
                        parentId: undefined,
                        children: allChildrens,
                        disabled: false,
                    });
                }
            });

            variantValueId.value = newVariantTypesValues;
            varaitionTypeValues.value = allLists;
        };

        const getVariantNameById = (id) => {
            const searchedVariant = find(props.variations, ["xid", id]);

            return searchedVariant ? searchedVariant.name : "-";
        };

        const addNewVariant = () => {
            addEditForm.value = {
                unique_id: generateRandomTableKey(), // Key used in edit time for newly added item
                image: undefined,
                image_url: undefined,
                variant_id: variantId.value,
                variant_value_id: variantValueId.value,
                xid: "",
                item_code: "",
                purchase_price: "",
                purchase_tax_type: "exclusive",
                sales_price: "",
                sales_tax_type: "exclusive",
                mrp: "",
                opening_stock: 0,
                opening_stock_date: undefined,
                stock_quantitiy_alert: null,
            };

            visible.value = true;
        };

        const editItem = (record) => {
            addEditForm.value = {
                unique_id: record.unique_id,
                image: record.image,
                image_url: record.image_url,
                variant_id: record.x_variant_id ? record.x_variant_id : record.variant_id,
                variant_value_id: record.x_variant_value_id
                    ? record.x_variant_value_id
                    : record.variant_value_id,
                xid: record.xid,
                item_code: record.item_code,
                purchase_price: record.purchase_price,
                purchase_tax_type: record.purchase_tax_type,
                sales_price: record.sales_price,
                sales_tax_type: record.sales_tax_type,
                mrp: record.mrp,
                opening_stock: record.opening_stock,
                opening_stock_date: record.opening_stock_date,
                stock_quantitiy_alert: record.stock_quantitiy_alert,
            };

            addEditType.value = "edit";
            visible.value = true;
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "/products/check-variants",
                data: addEditForm.value,
                success: (res) => {
                    if (
                        some(variantTableData.value, [
                            "unique_id",
                            addEditForm.value.unique_id,
                        ])
                    ) {
                        variantTableData.value = map(
                            variantTableData.value,
                            (newData) => {
                                return newData.unique_id == addEditForm.value.unique_id
                                    ? addEditForm.value
                                    : newData;
                            }
                        );
                    } else {
                        variantTableData.value = [
                            {
                                ...addEditForm.value,
                            },
                            ...variantTableData.value,
                        ];
                    }

                    emit("variant_added", variantTableData.value);

                    variantId.value = [];
                    variantValueId.value = [];
                    visible.value = false;
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            visible.value = false;
        };

        watch(
            () => props.showTable,
            (newVal, oldVal) => {
                setVariantTableData(newVal.formData.variations);
            }
        );

        const variantValueSelected = (value, options) => {
            var selectedParent = find(props.variations, ["xid", options.parentId]);

            var newVariantTypeList = [];
            forEach(variantValueId.value, (newVariationId) => {
                var isVariantInSelectedVariant = find(selectedParent.sub_variations, {
                    xid: newVariationId,
                });

                if (isVariantInSelectedVariant == undefined) {
                    newVariantTypeList.push(newVariationId);
                }
            });

            newVariantTypeList.push(value);

            variantValueId.value = newVariantTypeList;
        };

        const variantValueDeselected = (value, options) => {
            variantValueId.value = filter(variantValueId.value, (newVariationId) => {
                return newVariationId != value;
            });
        };

        return {
            appSetting,
            formatAmountCurrency,

            variantId,
            variantValueId,

            varaitionTypes,
            varaitionTypeValues,
            getValueByVariation,
            addNewVariant,

            addEditType,
            addEditForm,
            visible,
            onSubmit,
            onClose,

            variantTableData,
            variantTableColumns,
            generateRandomTableKey,
            getVariantNameById,
            editItem,

            rules,
            loading,

            modalWidth: window.innerWidth <= 991 ? "90%" : "35%",

            variantValueSelected,
            variantValueDeselected,
            setVariantTableData,
            setDirectVariantTableData,
        };
    },
});
</script>

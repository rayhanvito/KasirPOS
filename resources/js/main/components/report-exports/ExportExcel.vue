<template>
    <a-col :span="24">
        <a-button type="primary" @click="exportExcel">
            <FileExcelOutlined />
            {{ $t("common.excel") }}
        </a-button>
    </a-col>
</template>

<script>
import { ref } from "vue";
import { useStore } from "vuex";
import { find } from "lodash-es";
import { FileExcelOutlined } from "@ant-design/icons-vue";
import * as XLSX from "xlsx";
import allFields from "../../../common/composable/allFields";

export default {
    props: {
        exportType: {
            type: String,
            default: "",
        },
    },
    components: {
        FileExcelOutlined,
    },
    setup(props) {
        const store = useStore();
        const { getColumns } = allFields();
        const fileName = ref(props.exportType + ".xlsx");
        const sheetName = ref(props.exportType);

        const exportExcel = () => {
            var allExportDatas = store.state.auth.allExportData;
            var storeExportData = find(
                allExportDatas,
                (allExportData) => allExportData.export_type == props.exportType
            );

            var tablecolumn = getColumns(props.exportType);
            var tableData = storeExportData.data;

            let createXLSLFormatObj = [];
            let newXlsHeader = [];
            if (tablecolumn.length === 0) {
                return;
            }
            if (tableData.length === 0) {
                return;
            }
            tablecolumn.map((column) => {
                newXlsHeader.push(column.title);
            });
            createXLSLFormatObj.push(newXlsHeader);

            tableData.map((value) => {
                let innerRowData = [];
                tablecolumn.map((val) => {
                    let fieldValue = value[val.dbKey];
                    if (val.dbKey.split(".").length > 1) {
                        fieldValue = getNestedValue(value, val.dbKey);
                    }
                    if (val.dataFormat && typeof val.dataFormat === "function") {
                        innerRowData.push(val.dataFormat(value));
                    } else {
                        innerRowData.push(fieldValue);
                    }
                });
                createXLSLFormatObj.push(innerRowData);
            });

            let workbook = XLSX.utils.book_new(),
                worksheet = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);

            /* calculate column width */
            worksheet["!cols"] = formatExcelCols(createXLSLFormatObj);

            XLSX.utils.book_append_sheet(workbook, worksheet, sheetName.value);
            XLSX.writeFile(workbook, fileName.value);
        };

        const formatExcelCols = (json) => {
            let widthArr = Object.keys(json[0]).map((key) => {
                return { width: key.length + 2 }; // plus 2 to account for short object keys
            });
            for (let i = 0; i < json.length; i++) {
                let value = Object.values(json[i]);
                for (let j = 0; j < value.length; j++) {
                    if (value[j] !== null && value[j].length > widthArr[j].width) {
                        widthArr[j].width = value[j].length + 3;
                    }
                }
            }
            return widthArr;
        };

        const getNestedValue = (object, string) => {
            string = string.replace(/\[(\w+)\]/g, ".$1");
            string = string.replace(/^\./, "");
            let a = string.split(".");
            for (let i = 0, n = a.length; i < n; ++i) {
                let k = a[i];
                if (k in object) {
                    object = object[k];
                } else {
                    return;
                }
            }

            return object;
        };

        return {
            exportExcel,
        };
    },
};
</script>

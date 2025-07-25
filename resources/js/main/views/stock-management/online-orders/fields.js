import { ref, reactive, computed } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
	const url = "online-orders?fields=id,xid,unique_id,invoice_number,order_type,order_date,tax_amount,discount,shipping,subtotal,paid_amount,due_amount,order_status,payment_status,total,tax_rate,staff_user_id,x_staff_user_id,staffMember{id,xid,name,profile_image,profile_image_url,user_type},user_id,x_user_id,user{id,xid,user_type,name,profile_image,profile_image_url,phone},orderPayments{id,xid,amount,payment_id,x_payment_id},orderPayments:payment{id,xid,amount,payment_mode_id,x_payment_mode_id,date,notes},orderPayments:payment:paymentMode{id,xid,name},items{id,xid,product_id,x_product_id,single_unit_price,unit_price,quantity,tax_rate,total_tax,tax_type,total_discount,subtotal},items:product{id,xid,name,image,image_url},cancelled,terms_condition,shippingAddress{id,xid,order_id,name,email}";
	const hashableColumns = ['user_id'];
	const { t } = useI18n();
	const columns = ref([
		{
			title: t(`stock.invoice_number`),
			dataIndex: "invoice_number",
		},
		{
			title: t(`online_orders.online_orders_date`),
			dataIndex: "order_date",
		},
		{
			title: t(`online_orders.user`),
			dataIndex: "user_id",
		},
		{
			title: t(`online_orders.online_orders_status`),
			dataIndex: "order_status",
		},
		{
			title: t("payments.paid_amount"),
			dataIndex: "paid_amount",
		},
		{
			title: t("payments.total_amount"),
			dataIndex: "total_amount",
		},

		{
			title: t("payments.payment_status"),
			dataIndex: "payment_status",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	]);

	const preFetchData = reactive({
		users: [],
	});

	const initPaymentData = {
		date: undefined,
		payment_mode_id: undefined,
		amount: "",
		notes: "",
	};

	const filterableColumns = [
		{
			key: "name",
			value: t("tax.name")
		},
	];

	const paymentModeColumns = [
		{
			title: t("payments.date"),
			dataIndex: "date",
		},
		{
			title: t("payments.amount"),
			dataIndex: "amount",
		},
		{
			title: t("payments.payment_mode"),
			dataIndex: "payment_mode_id",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];

	const orderItemDetailsColumns = [
		{
			title: t("product.product"),
			dataIndex: "product_id",
		},
		{
			title: t("product.quantity"),
			dataIndex: "quantity",
		},
		{
			title: t("product.unit_price"),
			dataIndex: "single_unit_price",
		},
		{
			title: t("product.discount"),
			dataIndex: "total_discount",
		},
		{
			title: t("product.tax"),
			dataIndex: "total_tax",
		},
		{
			title: t("product.subtotal"),
			dataIndex: "subtotal",
		},
	];

	const getPreFetchData = () => {
		const usersPromise = axiosAdmin.get("customers?limit=10000");

		Promise.all([usersPromise]).then(
			([usersResponse]) => {
				preFetchData.users = usersResponse.data;
			}
		);
	};

	return {
		url,
		columns,
		hashableColumns,
		initPaymentData,
		filterableColumns,
		paymentModeColumns,
		preFetchData,
		getPreFetchData,
		orderItemDetailsColumns,
	}
}

export default fields;
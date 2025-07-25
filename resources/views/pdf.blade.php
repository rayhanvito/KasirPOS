<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;

            font-size: 14px;
            line-height: 18px;
            color: #555;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);;
        }

        body {
            font-family: 'Heebo', sans-serif;
        }

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top img {
            width: 150px;
        }

        .invoice {
            display: flex;
            align-items: center;
            width: 210px;
            font-size: 15px;
            justify-content: space-between;
            font-weight: 500;
            height: 25px;
        }

        .text-center {
            text-align: center;
        }

        .costomer {
            background: rgb(54, 54, 54);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 50px;
            padding: 0 20px;
        }

        .costomer p:last-child {
            margin-right: 250px;
        }

        .table-one {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 0 20px 20px 20px;
            border: 1px solid #a9a9a9;
            margin-top: -10px;
        }

        .table-one .avery {
            width: 28%;
            margin-right: 100px;
        }

        .example {
            width: 30%;
        }

        .example h3 {
            font-size: 17px;
            height: 4px;
        }

        .avery h3 {
            width: 87%;
            margin-bottom: -13px;
        }

        .table-one p {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .table-one span {
            display: block;
            font-weight: 500;
        }

        .invoice-box table tr.item td {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            padding: 10px;
        }
        .invoice-box table tr.item.item-two td {
            border-right: 1px solid #fff;
            border-bottom: none;
        }
        .invoice-box table tr.item.item-three td {
            border-top: 1px solid #a9a9a9;
        }

		/** RTL **/
		.invoice-box.rtl {
			direction: rtl;
		}

        .table-two table thead {
            background: rgb(54, 54, 54);
            color: #fff;
        }

        .table-two thead th {
            padding: 6px;
        }

        .table-two table {
            width: 100%;
        }
        tr.item.heading-tow {
            background: #f8f8f8;
        }
        tr.item.heading-tow {
            background: #f8f8f8;
        }




        .invoice-box table tr.heading td{
            background: #363636;
            color: #fff;
            padding: 6px;
        }

        .status{
            border: 1px solid #a9a9a9;
            margin-top: 40px;
            display: flex;
            align-items: center;
        }
        .table-three {
            border-left: 1px solid #a9a9a9;
            width: 100%;
        }

        .invoice-box table tr.item-four td {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: end;
            padding: 5px;
        }
        .invoice-box table tr.item-four td:last-child {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: start;
            padding: 5px;
        }
        .invoice-box table tr.item-four th {
            border-right: 1px solid #a9a9a9;
            border-bottom: 1px solid #a9a9a9;
            text-align: end;
            padding: 5px;
        }

        .status-due span{
            display: block;
            font-size: 15px;
        }
        .status-due{
            padding-left: 10px;
        }
        span.paid{
            margin-top: 15px;
        }
        .status-due h4{
            color: #000;
            font-size: 18px;
            margin: 0 0 15px 0;
        }
        table{
            width: 400px;
        }
        .signertuer span{
            display: block;
            font-size: 16px;
            font-weight: 500;
        }
        .signertuer img{
            width: 100px;
            margin: 10px 0;
        }
        .signertuer{
            display: flex;
            align-items: center;
            justify-content: end;
            margin-right: 70px;
        }
        .divider {
            line-height: 1.5715;
            color: #000000d9;
            border-top: 1px solid rgba(0, 0, 0, .7);
            margin: 30px 0;
        }
        .trem span{
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }
        .trem p{
            color: #000;
        }
        table.information{
            width: 100%;
        }


        .text-right {
            text-align: right;
        }

        .p-5 {
            padding-bottom: 5px;
        }

        .space-10 {
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .space-3 {
            padding-bottom: 3px;
            margin-bottom: 3px;
        }

        .pl-15 {
            padding-left: 15px;
        }

        .pt-15 {
            padding-top: 15px;
        }

        .pb-15 {
            padding-bottom: 15px;
        }

        .mt-20 {
            margin-top: 10px;
        }

        .clearfix {
			display: block;
			clear: both;
		}
    </style>
</head>

<body>

    <div class="invoice-box">
        <table class="company-details" cellpadding="0" cellspacing="0" style="width: 100%">
			<tr>
				<td style="padding-bottom: 20px;">
					<img src="{{ $company->light_logo_url }}" style="width: 200px; margin-top: 5px;" />
				</td>
				<td style="margin-left: 100px; padding-left: 30%; padding-bottom: 20px;">
					<table style="width: 100%">
                        <tr style="text-align: left">
                            <td class="space-10">
                                <span style="font-size: 28px; font-weight: bolder">
									@if($order->order_type == "purchases")
                                    {{ $traslations['purchase_invoice'] }}
                                    @elseif($order->order_type == "purchase-returns")
                                    {{ $traslations['purchase_return_invoice'] }}
                                    @elseif($order->order_type == "sales-returns")
                                    {{ $traslations['sales_return_invoice'] }}
                                    @elseif($order->order_type == "sales")
                                    {{ $traslations['sales_invoice'] }}
                                    @elseif($order->order_type == "quotations")
                                    {{ $traslations['quotation_invoice'] }}
                                    @endif
								</span>
                            </td>
                        </tr>
						<tr>
							<table cellpadding="0" cellspacing="0" style="width: 100%">
                                <tr>
                                    <td class="space-3" style="font-weight: bold;">
                                        @if($order->order_type == "sales" || $order->order_type == "sales-returns" || $order->order_type == "quotations")
                                        Ref
                                        @else
                                        {{ $traslations['invoice'] }}
                                        @endif
                                    </td>
                                    <td class="space-3 text-right">{{ $order->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="space-3" style="font-weight: bold;">{{ $traslations['order_date'] }}</td>
                                    <td class="space-3 text-right">{{ $order->order_date->format($dateTimeFormat) }}</td>
                                </tr>
                                <tr>
                                    <td class="space-3" style="font-weight: bold;">{{ $traslations['order_status'] }}</td>
                                    <td class="space-3 text-right">{{ $orderStatusText }}</td>
                                </tr>
                                <tr>
                                    <td class="space-3" style="font-weight: bold;">Sold By</td>
                                    <td class="space-3 text-right">{{ $staffMember->name }}</td>
                                </tr>
                            </table>
						</tr>
					</table>
				</td>
			</tr>
		</table>

        <table class="information"  cellpadding="4px" cellspacing="0" style="border: 1px solid #a9a9a9;">
            <tr class="heading" >
                <td style="width: 40%; padding: 12px;" >
                    Seller
                </td>
                <td style="width: 20%"></td>
                <td style="width: 40%; padding-left: 15px;">
                    Buyer
                </td>
            </tr>
            <tr>
                <td class="pl-15 pt-15 pb-15" style="vertical-align:top;">
                    <span style="font-size: 16px; font-weight: bold">{{ $company->name }}</span>  <br>
                    <span>{{ $company->address }}</span><br>
                    <span>{{ $company->email }}</span><br>
                    <span>{{ $company->phone }}</span>
                </td>
                <td class="pl-15 pt-15 pb-15" style="vertical-align:top;"></td>
                <td class="pl-15 pt-15 pb-15" style="vertical-align:top;">
                    @if($order->order_type == 'stock-transfers')
                        <span style="font-size: 16px; font-weight: bold">{{ $order->warehouse->name }}</span><br />
                        @if($order->warehouse->addres)
                        {{ $order->warehouse->address  }} <br />
                        @endif
                        @if($order->warehouse->phone)
                        {{ $order->warehouse->phone }} <br />
                        @endif
                        {{ $order->warehouse->email }}
                    @else
                        <span style="font-size: 16px; font-weight: bold">{{ $order->user->name }}</span><br />
                        @if($order->user->address || $order->user->city || $order->user->zipcode)
                        {{ $order->user->address .'' .  $order->user->city .''. $order->user->zipcode }} <br />
                        @endif
                        @if($order->user->phone)
                        {{ $order->user->phone }} <br />
                        @endif
                        {{ $order->user->email }}
                    @endif
                </td>
            </tr>
        </table>
        <div class="table-two">
            <table cellpadding="4px" cellspacing="0">

                <tbody>
                    <tr class="heading">
                        <td>#</td>
                        <td>{{ $traslations['product'] }}</td>
                        <td>{{ $traslations['quantity'] }}</td>
                        @if($order->warehouse->show_mrp_on_invoice)
                        <td>{{ $traslations['mrp'] }}</td>
                        @endif
                        <td>{{ $traslations['unit_price'] }}</td>
                        <td>{{ $traslations['total'] }}</td>
                    </tr>

                    @foreach($order->items as $item)
                    <tr class="item heading-tow">
                        <td style="border-left: 1px solid #a9a9a9;">{{ $loop->iteration }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity . ' ' . $item->unit->short_name }}</td>
                        @if($order->warehouse->show_mrp_on_invoice)
                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->mrp) }}</td>
                        @endif
                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->single_unit_price) }}</td>
                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="status">
            <table cellpadding="4px" cellspacing="0" style="width: 100%">
                <tr>
                    <td>
                        <div class="status-due">
                            <div>
                                <h4>{{ $traslations['payment_status'] }} : {{ $paymentStatusText }}</h4>
                                <span>{{ $traslations['paid_amount'] }} : {{ App\Classes\Common::formatAmountCurrency($company->currency, $order->paid_amount) }}</span>
                                <span>{{ $traslations['due_amount'] }}: {{ App\Classes\Common::formatAmountCurrency($company->currency, $order->due_amount) }}</span>
                                <span class="paid">
                                    {{ $traslations['payment_mode'] }}:
                                    @if($order->orderPayments)
                                        @foreach ($order->orderPayments as $currentOrderPayment)
                                            {{ App\Classes\Common::formatAmountCurrency($company->currency, $currentOrderPayment->amount) }}
                                            @if($currentOrderPayment->payment && $currentOrderPayment->payment->paymentMode && $currentOrderPayment->payment->paymentMode->name)
                                                ({{ $currentOrderPayment->payment->paymentMode->name }})
                                            @endif
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>

                    <td style="padding: 0px;">
                        <div class="table-three">
                            <table cellpadding="0px" cellspacing="0" style="width: 100%">
                                <tbody>


                                    <tr class="item-four">
                                        <td>{{ $traslations['subtotal'] }}</td>
                                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->subtotal) }}</td>
                                    </tr>
                                    <tr class="item-four">
                                        <td>{{ $traslations['tax'] }}</td>
                                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }} ({{ $order->tax_rate }}%)</td>
                                    </tr>
                                    <tr class="item-four">
                                        <td>{{ $traslations['discount'] }}</td>
                                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->discount) }}</td>
                                    </tr>
                                    <tr class="item-four">
                                        <td>{{ $traslations['shipping'] }}</td>
                                        <td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->shipping) }}</td>
                                    </tr>
                                    <tr class="item-four">
                                        <td style="border-bottom: 0px;"><b>{{ $traslations['total'] }}</b></td>
                                        <td style="border-bottom: 0px;">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        <div class="mt-20">
			<div style="width: 65%; float: left;">
				<p class="mt-20" style="font-weight: bold; font-size: 14px;">
                    {{ $traslations['notes'] }}:
                </p>
                <p>{{ $order->notes }}</p>
			</div>
			<div style="width: 30%; float: right;">
				<div class="signertuer">
                    <div>
                        <span>Authorized person</span>
                        <img src="{{ $warehouse->signature_url }}"  style="width: 200px; margin-top: 5px;" />
                        </span>
                    </div>
                </div>
			</div>
			<div class="clearfix"></div>
		</div>


        <div class="divider"></div>
        <table cellpadding="4px" cellspacing="0" style="width: 100%">
            <tr>
                <td style="width: 50%; vertical-align:top;">
                    <span style="font-weight: bold;">{{ $traslations['terms_condition'] }}</span>
                    <p>{{ $warehouse->terms_condition }}</p>
                </td>
                <td style="width: 50%; vertical-align:top;">
                    <span style="font-weight: bold;">Bank Details</span>
                    <p>{{ $warehouse->bank_details }}</p>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>

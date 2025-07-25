<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{{ $order->invoice_number }}</title>

	<style>
		@page {
			margin: 20px;
		}

        /* * {
			font-family: DejaVu Sans, sans-serif;
		} */

		body {
			margin: 20px;
			position: relative;
			width: 100%;
			height: auto;
			margin: 0 auto;
			color: #555555;
			background: #FFFFFF;
			font-size: 12px;
		}

		.invoice-box {
			max-width: 800px;
			margin: auto;

			font-size: 14px;
			line-height: 18px;
			color: #555;
		}

		.invoice-box table {
			width: 100%;
			line-height: inherit;
			text-align: left;
		}

		.invoice-box table td {
			padding: 5px;
			vertical-align: top;
		}

		.invoice-box table tr td:nth-child(6) {
			text-align: right;
		}

		.invoice-box table tr.top table td {
			padding-bottom: 20px;
		}

		.invoice-box table tr.information table td {
			padding-bottom: 40px;
		}

		.invoice-box table tr.heading td {
			background: #eee;
			border-bottom: 1px solid #ddd;
			font-weight: bold;
		}

		.invoice-box table tr.main-heading td {
			background: rgb(218, 218, 218);
			border-bottom: 1px solid #ddd;
			font-weight: bold;
		}

		.invoice-box table tr.details td {
			padding-bottom: 20px;
		}

		.invoice-box table tr.item td {
			border-bottom: 1px solid #eee;
		}

		.invoice-box table tr.item.last td {
			border-bottom: none;
		}

		@media only screen and (max-width: 600px) {
			.invoice-box table tr.top table td {
				width: 100%;
				display: block;
				text-align: left;
			}

			.invoice-box table tr.information table td {
				width: 100%;
				display: block;
				text-align: left;
			}
		}

		/** RTL **/
		.invoice-box.rtl {
			direction: rtl;
			font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
		}

		.invoice-box.rtl table {
			text-align: left;
		}

		.invoice-box.rtl table tr td:nth-child(5) {
			text-align: right;
		}

		/** New CSS **/
		.text-left {
			text-align: left;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.divider {
			line-height: 1.5715;
			color: #000000d9;
			border-top: 1px solid rgba(0, 0, 0, .10);
		}

		.company-info {
			text-align: left;
		}

		.company-title {
			font-weight: bold;
			font-size: 18px;
		}

		p {
			margin: 0;
		}

		.clearfix {
			display: block;
			clear: both;
		}

		.mt-10 {
			margin-top: 10px;
		}

		.mt-20 {
			margin-top: 20px;
		}

		.total-bg {
			background: #eee;
		}
	</style>
</head>

<body>
	<div class="invoice-box">
		<table cellpadding="0" cellspacing="0">
			<tr class="main-heading">
				@if($order->order_type == "purchases")
				<td class="text-center">{{ $traslations['purchase_invoice'] }}</td>
				@elseif($order->order_type == "purchase-returns")
				<td class="text-center">{{ $traslations['purchase_return_invoice'] }}</td>
				@elseif($order->order_type == "sales-returns")
				<td class="text-center">{{ $traslations['sales_return_invoice'] }}</td>
				@elseif($order->order_type == "sales")
				<td class="text-center">{{ $traslations['sales_invoice'] }}</td>
                @elseif($order->order_type == "quotations")
				<td class="text-center">{{ $traslations['quotation_invoice'] }}</td>
				@endif
			</tr>
		</table>

		<table cellpadding="0" cellspacing="0">
			<tr class="top">
				<td>
					<img src="{{ $company->light_logo_url }}" style="width: 200px; margin-top: 5px;" />
				</td>
				<td colspan="2">
					<table>
						<tr>
							<td style="vertical-align: middle;">
								<img src="{{ $company->logo_url }}" style="width: 150px">
							</td>
							<td class="text-right">
								<span class="company-title">
									{{ $company->name }} <br>
								</span>
								<span>
									{{ $company->address }} <br>
									{{ $company->email }} <br>
									{{ $company->phone }}
								</span>
							</td>


						</tr>
					</table>
				</td>
			</tr>

		</table>

		<div class="divider"></div>

		<table cellpadding="0" cellspacing="0">
			<tr class="information">
				<td colspan="2">
					<table>
						<tr>
							<td class="text-left">
								{{ $traslations['invoice'] }} #: {{ $order->invoice_number }}<br />
								{{ $traslations['order_date'] }}: {{ $order->order_date->format($dateTimeFormat) }}<br />
								{{ $traslations['order_status'] }}: {{ $orderStatusText }}<br />
								{{ $traslations['payment_status'] }}: {{ $paymentStatusText }}
							</td>

                            @if($order->order_type == 'stock-transfers')
							<td class="text-right">
								<b>{{ $traslations['bill_to'] }}</b><br />
								{{ $order->warehouse->name }}<br />
								@if($order->warehouse->addres)
								{{ $order->warehouse->address  }} <br />
								@endif
								@if($order->warehouse->phone)
								{{ $order->warehouse->phone }} <br />
								@endif
								{{ $order->warehouse->email }}
							</td>
                            @else
                                <td class="text-right">
                                    <b>@if($order->order_type == "sales" || $order->order_type == "sales-returns") {{ $traslations['bill_to'] }} @elseif($order->order_type == "purchases" || $order->order_type == "purchase-returns") Bill From @endif:</b><br />
                                    {{ $order->user->name }}<br />
                                    @if($order->user->address || $order->user->city || $order->user->zipcode)
                                    {{ $order->user->address .'' .  $order->user->city .''. $order->user->zipcode }} <br />
                                    @endif
                                    @if($order->user->phone)
                                    {{ $order->user->phone }} <br />
                                    @endif
                                    {{ $order->user->email }}
                                </td>
                            @endif
						</tr>
					</table>
				</td>
			</tr>
		</table>


		<table cellpadding="0" cellspacing="0">

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
			<tr class="item">
				<td>{{ $loop->iteration }}</td>
				<td>{{ $item->product->name }}</td>
				<td>{{ $item->quantity . ' ' . $item->unit->short_name }}</td>
                @if($order->warehouse->show_mrp_on_invoice)
				<td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->mrp) }}</td>
                @endif
				<td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->single_unit_price) }}</td>
				<td>{{ App\Classes\Common::formatAmountCurrency($company->currency, $item->subtotal) }}</td>
			</tr>
			@endforeach

		</table>

		<div class="divider"></div>

		<div class="mt-20">
			<div style="width: 65%; float: left;">
				<p class="mt-20" style="font-weight: bold; font-size: 14px;">
					{{ $traslations['notes'] }}:
				</p>
				<p>{{ $order->notes }}</p>
                <p class="mt-20" style="font-weight: bold; font-size: 14px;">
					{{ $traslations['terms_condition'] }}:
				</p>
				<p>{{ $order->terms_condition }}</p>
			</div>
			<div style="width: 30%; float: right;">
				<table cellpadding="0" cellspacing="0" class="mt-10">
					<tr>
						<td>{{ $traslations['subtotal'] }}</td>
						<td class="text-right">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->subtotal) }}</td>
					</tr>
					<tr>
						<td>{{ $traslations['tax'] }}</td>
						<td class="text-right">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->tax_amount) }} ({{ $order->tax_rate }}%)</td>
					</tr>
					<tr>
						<td>{{ $traslations['discount'] }}</td>
						<td class="text-right">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->discount) }}</td>
					</tr>
					<tr>
						<td>{{ $traslations['shipping'] }}</td>
						<td class="text-right">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->shipping) }}</td>
					</tr>
					<tr>
						<td class="total-bg"><b>{{ $traslations['total'] }}</b></td>
						<td class="text-right total-bg">{{ App\Classes\Common::formatAmountCurrency($company->currency, $order->total) }}</td>
					</tr>
				</table>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="divider mt-10"></div>
		<table cellpadding="0" cellspacing="0">
			<tr class="heading">
				<td>{{ $traslations['total_items'] }} / {{ $traslations['qty'] }} : {{ $order->total_items . ' / ' . $order->total_quantity }}</td>
				<td>{{ $traslations['paid_amount'] }}: {{ App\Classes\Common::formatAmountCurrency($company->currency, $order->paid_amount) }}</td>
				<td>{{ $traslations['due_amount'] }}: {{ App\Classes\Common::formatAmountCurrency($company->currency, $order->due_amount) }}</td>
			</tr>
		</table>
		<div class="divider"></div>

        <table cellpadding="0" cellspacing="0">
			<tr class="heading text-center">
				<td>
                    {{ $traslations['payment_mode'] }} :
                    @if($order->orderPayments)
                    @foreach ($order->orderPayments as $currentOrderPayment)
                        <span style="margin-right: 10px;">
                            {{ App\Classes\Common::formatAmountCurrency($company->currency, $currentOrderPayment->amount) }}
                            @if($currentOrderPayment->payment && $currentOrderPayment->payment->paymentMode && $currentOrderPayment->payment->paymentMode->name)
                                ({{ $currentOrderPayment->payment->paymentMode->name }})
                            @endif
                        </span>
                    @endforeach
                    @else
                        -
                    @endif
                </td>
			</tr>
		</table>

	</div>
</body>

</html>

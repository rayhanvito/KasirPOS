<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class MercadoPaymentController extends Controller
{

    public $token;
    public $is_enabled;
    public $currancy;
    public $mode;
    protected $invoiceData;

    public function paymentConfig()
    {

            $payment_setting = Utility::getAdminPaymentSetting();


        $this->token      = isset($payment_setting['mercado_access_token']) ? $payment_setting['mercado_access_token'] : '';
        $this->mode       = isset($payment_setting['mercado_mode']) ? $payment_setting['mercado_mode'] : '';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
        $this->currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : '';


        return $this;
    }

    public function companyPaymentConfig()
    {

            $payment_setting = Utility::getCompanyPaymentSetting(!empty($this->invoiceData)?$this->invoiceData->created_by:0);

            $setting = Utility::settingsById(!empty($this->invoiceData)?$this->invoiceData->created_by:0);


        $this->token      = isset($payment_setting['mercado_access_token']) ? $payment_setting['mercado_access_token'] : '';
        $this->mode       = isset($payment_setting['mercado_mode']) ? $payment_setting['mercado_mode'] : '';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
        $this->currency = isset($setting['site_currency']) ? $setting['site_currency'] : '';


        return $this;
    }

    public function planPayWithMercado(Request $request)
    {

        $payment = $this->paymentConfig();

        $planID   = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan     = Plan::find($planID);
        $authuser = \Auth::user();

        $coupons_id = '';
        if($plan)
        {
            $price = $plan->price;
            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;
                    $coupons_id             = $coupons->id;
                    if($usedCoupun >= $coupons->limit)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                }
                else
                {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {

                $authuser->plan = $plan->id;

                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if($assignPlan['is_success'] == true && !empty($plan))
                {
                    if(!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '')
                    {
                        try
                        {
                            $authuser->cancel_subscription($authuser->id);
                        }
                        catch(\Exception $exception)
                        {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    $orderID = time();
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => ($this->currency),
                            'txn_id' => '',
                            'payment_type' => __('Mercado'),
                            'payment_status' => 'success',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );


                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Plan fail to upgrade.'));
                }
            }

            \MercadoPago\SDK::setAccessToken($this->token);
            try
            {

                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item              = new \MercadoPago\Item();
                $item->title       = "Plan : " . $plan->name;
                $item->quantity    = 1;
                $item->unit_price  = (float)$price;
                $preference->items = array($item);


                $success_url = route(
                    'plan.mercado', [
                        $request->plan_id,
                        $price,
                        'payment_frequency=' . $request->mercado_payment_frequency,
                        'coupon_id=' . $coupons_id,
                        'flag' => 'success',
                    ]
                );
                $failure_url = route(
                    'stripe', [
                        Crypt::encrypt($request->plan_id),
                    ]
                );
                $pending_url = route(
                    'plan.mercado', [
                        $request->plan_id,
                        $price,
                        'payment_frequency=' . $request->mercado_payment_frequency,
                        'coupon_id=' . $coupons_id,
                        'flag' => 'pending',
                    ]
                );

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name    = \Auth::user()->name;
                $payer->email   = \Auth::user()->email;
                $payer->address = array(
                    "street_name" => '',
                );
                if($this->mode == 'live')
                {
                    $redirectUrl = $preference->init_point;
                }
                else
                {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return redirect($redirectUrl);
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('error', $e->getMessage());
            }

        }
        else
        {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }

    }

    public function getPaymentStatus(Request $request, $plan, $price)
    {

        $payment = $this->paymentConfig();

        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan    = Plan::find($planID);
        $user    = \Auth::user();
        $orderID = time();
        if($plan)
        {
            try
            {

                if($plan && $request->has('status'))
                {

                    if($request->status == 'approved' && $request->flag == 'success')
                    {


                        if($request->has('coupon_id') && $request->coupon_id != '')
                        {
                            $coupons = Coupon::find($request->coupon_id);

                            if(!empty($coupons))
                            {
                                $userCoupon         = new UserCoupon();
                                $userCoupon->user   = $user->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order  = $orderID;
                                $userCoupon->save();

                                $usedCoupun = $coupons->used_coupon();
                                if($coupons->limit <= $usedCoupun)
                                {
                                    $coupons->is_active = 0;
                                    $coupons->save();
                                }
                            }
                        }
                        Utility::referralTransaction($plan);

                        $order                 = new Order();
                        $order->order_id       = $orderID;
                        $order->name           = $user->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = $price;
                        $order->price_currency = $this->currency;
                        $order->txn_id         = isset($request->TXNID) ? $request->TXNID : '';
                        $order->payment_type   = __('Mercado');
                        $order->payment_status = 'success';
                        $order->receipt        = '';
                        $order->user_id        = $user->id;
                        $order->save();

                        $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);
                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                        }
                        else
                        {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            }
        }
    }

    public function customerPayWithMercado(Request $request)
    {


        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);

        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','mercado')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Mercado Pago.'));
        }
        
        $user      = User::find($invoice->created_by);
        $this->invoiceData = $invoice;


        $this->companyPaymentConfig();

        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );
        if($validator->fails())
        {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }

        $user      = User::find($invoice->created_by);
        if($invoice->getDue() < $request->amount)
        {
            return redirect()->back()->with('error', __('Not currect amount'));

        }
        \MercadoPago\SDK::setAccessToken($this->token);
        try
        {

            // Create a preference object
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item              = new \MercadoPago\Item();
            $item->title       = "Invoice : " . $invoice->invoice_id;
            $item->quantity    = 1;
            $item->unit_price  = (float)$request->amount;
            $preference->items = array($item);

            $success_url             = route(
                'customer.mercado', [
                                      encrypt($invoice->id),
                                      'amount' => (float)$request->amount,
                                      'flag' => 'success',
                                  ]
            );
            $failure_url             = route(
                'customer.mercado', [
                                      encrypt($invoice->id),
                                      'flag' => 'failure',
                                  ]
            );
            $pending_url             = route(
                'customer.mercado', [
                                      encrypt($invoice->id),
                                      'flag' => 'pending',
                                  ]
            );
            $preference->back_urls   = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url,
            );
            $preference->auto_return = "approved";
            $preference->save();

            // Create a customer object
            $payer = new \MercadoPago\Payer();
            // Create payer information
            $payer->name    = $user->name;
            $payer->email   = $user->email;
            $payer->address = array(
                "street_name" => '',
            );

            if($this->mode == 'live')
            {
                $redirectUrl = $preference->init_point;
            }
            else
            {
                $redirectUrl = $preference->sandbox_init_point;
            }

            return redirect($redirectUrl);
        }
        catch(Exception $e)
        {

            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function getInvoicePaymentStatus(Request $request,$invoice_id)
    {
        $this->companyPaymentConfig();


        if(!empty($invoice_id))
        {
            $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);

            $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
            $settings = Utility::settingsById($invoice->created_by);


            if($invoice && $request->has('status'))
            {
                try
                {

                    if($request->status == 'approved' && $request->flag == 'success')
                    {
                        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','mercado')->first();
                        $payments = InvoicePayment::create(
                            [
                                'invoice_id' => $invoice_id,
                                'date' => date('Y-m-d'),
                                'amount' => $request->has('amount') ? $request->amount : 0,
                                'account_id' => $account->id,
                                'payment_method' => 1,
                                'order_id' => $orderID,
                                'payment_type' => __('Mercado'),
                                'receipt' => '',
                                'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                            ]
                        );

                        $invoice = Invoice::find($invoice->id);


                        if($invoice->getDue() <= 0)
                        {
                            Invoice::change_status($invoice->id, 4);
                        }
                        else
                        {
                            Invoice::change_status($invoice->id, 3);
                        }

                        Utility::addOnlinePaymentData($payments , $invoice , 'mercado');                        
                        
                        //for customer balance update
                        Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                        //for bank balance update
                        Utility::bankAccountBalance($account->id, $request->amount, 'credit');


                        //For Notification
                        $setting  = Utility::settingsById($invoice->created_by);
                        $customer = Customer::find($invoice->customer_id);
                        $notificationArr = [
                            'payment_price' => $request->amount,
                            'invoice_payment_type' => 'Mercado',
                            'customer_name' => $customer->name,
                        ];
                        //Slack Notification
                        if(isset($setting['payment_notification']) && $setting['payment_notification'] ==1)
                        {
                            Utility::send_slack_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //Telegram Notification
                        if(isset($setting['telegram_payment_notification']) && $setting['telegram_payment_notification'] == 1)
                        {
                            Utility::send_telegram_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                        }

                        //Twilio Notification
                        if(isset($setting['twilio_payment_notification']) && $setting['twilio_payment_notification'] ==1)
                        {
                            Utility::send_twilio_msg($customer->contact,'new_invoice_payment', $notificationArr,$invoice->created_by);
                        }
                        //webhook
                        $module ='New Invoice Payment';
                        $webhook=  Utility::webhookSetting($module,$invoice->created_by);
                        if($webhook)
                        {
                            $parameter = json_encode($invoice);
                            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                            if($status == true)
                            {
                                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));
                            }
                            else
                            {
                                return redirect()->back()->with('error', __('Payment successfully, Webhook call failed.'));
                            }
                        }


                        return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));

                    }
                    else
                    {
                        return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction fail'));
                    }
                }
                catch(\Exception $e)
                {
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice not found!'));
                }
            }
            else
            {
                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice not found.'));
            }
        }
        else
        {
            return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PaytmWallet;

class PaytmPaymentController extends Controller
{
    protected $invoiceData;

    public function paymentConfig()
    {
         $payment_setting = Utility::getAdminPaymentSetting();

        config(
            [
                'services.paytm-wallet.env' => isset($payment_setting['paytm_mode']) ? $payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($payment_setting['paytm_merchant_id']) ? $payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' => isset($payment_setting['paytm_merchant_key']) ? $payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($payment_setting['paytm_industry_type']) ? $payment_setting['paytm_industry_type'] : '',
                ]
            );
    }

    public function companyPaymentConfig()
    {


            $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);

        config(
            [
                'services.paytm-wallet.env' => isset($payment_setting['paytm_mode']) ? $payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($payment_setting['paytm_merchant_id']) ? $payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' => isset($payment_setting['paytm_merchant_key']) ? $payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($payment_setting['paytm_industry_type']) ? $payment_setting['paytm_industry_type'] : '',
                ]
            );
    }

    public function planPayWithPaytm(Request $request)
    {
        $planID     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan       = Plan::find($planID);
        $authuser   = \Auth::user();
        $payment    = $this->paymentConfig();


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
                        return Utility::error_res(__('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                }
                else
                {
                    return Utility::error_res(__('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if($assignPlan['is_success'] == true && !empty($plan))
                {

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
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
                            'price_currency' => config('services.paytm-wallet.currency'),
                            'txn_id' => '',
                            'payment_type' => 'Paytm',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg']  = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                }
                else
                {
                    return Utility::error_res(__('Plan fail to upgrade.'));
                }
            }

            $call_back = route(
                'plan.paytm', [
                    $request->plan_id,
                    'coupon_id=' . $coupons_id,
                ]
            );

            $payment   = PaytmWallet::with('receive');

            $payment->prepare(
                [
                    'order' => uniqid(),
                    'user' => Auth::user()->id,
                    'mobile_number' => $request->mobile_number,
                    'email' => Auth::user()->email,
                    'amount' => $price,
                    'plan' => $plan->id,
                    'callback_url' => $call_back,
                ]
            );

            return $payment->receive();
        }
        else
        {
            return Utility::error_res(__('Plan is deleted.'));
        }

    }

    public function getPaymentStatus(Request $request, $plan)
    {

        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan    = Plan::find($planID);
        $user    = Auth::user();
        $orderID = time();

        if($plan)
        {
            // try
            // {
                $payment  = $this->paymentConfig();
                $transaction = PaytmWallet::with('receive');
                // $response    = $transaction->response();

                // if($transaction->isSuccessful())
                // {

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
                    $order->price          = isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0;
                    $order->price_currency = config('services.paytm-wallet.currency');
                    $order->txn_id         = isset($request->TXNID) ? $request->TXNID : '';
                    $order->payment_type   = __('paytm');
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
                // }
                // else
                // {
                //     return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                // }
            // }
            // catch(\Exception $e)
            // {

            //     return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            // }
        }
    }

    public function customerPayWithPaytm(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paytm')->first();
        if(!$account)
        {
            return redirect()->back()->with('error', __('Bank account not connected with Paytm.'));
        }
        
        $this->invoiceData = $invoice;
        $user      = User::find($invoice->created_by);

        $payment   = $this->companyPaymentConfig();
        if($invoice)
        {
            $price = $request->amount;
            if($price > 0)
            {
                $call_back = route(
                    'customer.paytm', [
                                       $request->invoice_id,
                                       $price
                                   ]
                );
                $payment   = PaytmWallet::with('receive');

                $payment->prepare(
                    [
                        'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                        'user' => $user->id,
                        'mobile_number' => $request->mobile,
                        'email' => $user->email,
                        'amount' => $price,
                        'invoice' => $invoice->id,
                        'callback_url' => $call_back,
                    ]
                );


                return $payment->receive();

            }
            else
            {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }

        }
        else
        {
            return Utility::error_res(__('Invoice is deleted.'));
        }

    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id,$amount)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);

        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings = Utility::settingsById($invoice->created_by);

        $this->invoiceData = $invoice;
        $payment  = $this->companyPaymentConfig();

        if($invoice)
        {
            // try
            // {
            //     $transaction = PaytmWallet::with('receive');

            //     $response    = $transaction->response();

                // if($transaction->isSuccessful())
                // {
                    $account = BankAccount::where('created_by' , $invoice->created_by)->where('payment_name','paytm')->first();
                    $payments = InvoicePayment::create(
                        [
                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'account_id' => $account->id,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Paytm'),
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

                    Utility::addOnlinePaymentData($payments , $invoice , 'paytm');                        

                    //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');
                    //for bank balance update
                    Utility::bankAccountBalance($account->id, $request->amount, 'credit');

                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Paytm',
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
                // }
                // else
                // {
                //     return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed! '));
                // }
            // }
            // catch(\Exception $e)
            // {

            //     return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice not found!'));
            // }
        }
    }
}

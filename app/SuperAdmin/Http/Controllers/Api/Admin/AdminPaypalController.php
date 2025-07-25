<?php

namespace App\SuperAdmin\Http\Controllers\Api\Admin;

use App\Http\Controllers\ApiBaseController;
use App\Models\Company;
use App\Models\PaymentGatewaySettings;
use App\Models\PaypalInvoice;
use App\Models\SubscriptionPlan;
use App\Scopes\CompanyScope;
use App\SuperAdmin\Models\GlobalCompany;
use App\SuperAdmin\Models\GlobalSettings;
use App\SuperAdmin\Models\PaymentTranscation;
use App\SuperAdmin\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Common\PayPalModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Vinkla\Hashids\Facades\Hashids;

/** All Paypal Details class **/

use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AdminPaypalController extends ApiBaseController
{
    private $provider;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $paypalSettings = GlobalSettings::withoutGlobalScope(CompanyScope::class)
            ->where('setting_type', 'payment_settings')
            ->where('name_key', 'paypal')
            ->first();
        $credential = (object) $paypalSettings->credentials;

        $globalCompany = GlobalCompany::select('id', 'currency_id')
            ->with(['currency' => function ($query) {
                return $query->withoutGlobalScope(CompanyScope::class)
                    ->select('id', 'name', 'position', 'symbol', 'code');
            }])
            ->first();

        if ($credential->paypal_mode == 'sandbox') {
            $config = [
                'mode'    => 'sandbox',
                'sandbox' => [
                    'client_id'         => $credential->paypal_client_id,
                    'client_secret'     => $credential->paypal_secret,
                    'app_id'            => '',
                ],

                'payment_action' => 'Sale',
                'currency'       => $globalCompany->currency->code,
                'notify_url'     => $credential->paypal_client_id,
                'locale'         => 'en_US',
                'validate_ssl'   => false,
            ];
        } else {
            $config = [
                'mode'    => 'live',
                'live' => [
                    'client_id'         => $credential->paypal_client_id,
                    'client_secret'     => $credential->paypal_secret,
                    'app_id'            => '',
                ],

                'payment_action' => 'Sale',
                'currency'       => $globalCompany->currency->code,
                'notify_url'     => url('/') . '/paypal/notify',
                'locale'         => 'en_US',
                'validate_ssl'   => true,
            ];
        }

        $this->provider = new PayPalClient($config);
        $this->provider->getAccessToken();
    }

    public static function createBillingPlanData($productId, $subscriptionPlanName, $currency, $interval, $price)
    {

        $planData = [
            "product_id"        => $productId,
            "name"              => $subscriptionPlanName . ' - ' . $currency . strval($price),
            "description"       => "Billing Plan of " . $subscriptionPlanName,
            "status"            => "ACTIVE",
            "billing_cycles"    =>
            [
                [
                    "frequency" =>
                    [
                        "interval_unit"     => $interval,
                        "interval_count"    => 1
                    ],
                    "tenure_type"       => "REGULAR",
                    "sequence"          => 1,
                    "total_cycles"      => 0,
                    "pricing_scheme"    =>
                    [
                        "fixed_price"   =>
                        [
                            "value"         => strval($price),
                            "currency_code" => $currency
                        ]
                    ]
                ]
            ],
            "payment_preferences" =>
            [
                "auto_bill_outstanding" => true,
                "setup_fee" =>
                [
                    "value"         => "0",
                    "currency_code" => $currency
                ],
                "setup_fee_failure_action"  => "CANCEL",
                "payment_failure_threshold" => 3
            ]
        ];


        return $planData;
    }

    public function createProduct($name, $description)
    {
        $data = [
            "name" => $name,
            "description" => $description,
            "type" => "SERVICE",
            "category" => "SOFTWARE",
        ];

        $request_id = 'create-product-' . time();

        return $this->provider->createProduct($data, $request_id);
    }

    public function paymentWithpaypal($planId, $type)
    {
        $company = company();
        $convertedId = Hashids::decode($planId);
        $subscriptionPlanId = $convertedId[0];
        $subscriptionPlan = SubscriptionPlan::where('id', $subscriptionPlanId)->first();


        $globalCompany = GlobalCompany::select('id', 'currency_id')
            ->with(['currency' => function ($query) {
                return $query->withoutGlobalScope(CompanyScope::class)
                    ->select('id', 'name', 'position', 'symbol', 'code');
            }])
            ->first();

        $productName = "Inventory Management";
        $product = $this->createProduct($productName, 'Inventory Management Service');

        $interval = $type == "annual" ? 'YEAR' : 'MONTH';
        $price = $type == "annual" ? $subscriptionPlan->annual_price : $subscriptionPlan->annual_price;

        $planData = $this->createBillingPlanData($product['id'], $subscriptionPlan->name, $globalCompany->currency->code, $interval, $price);

        $request_id = 'create-plan-' . time();
        $billingPlan = $this->provider->createPlan($planData, $request_id);

        // $this->companyName = $this->company->name;

        // Payment Dates
        $today = Carbon::now();
        if ($type == "annual") {
            $nextPayDate = $today->addYear();
        } else {
            $nextPayDate = $today->addMonth();
        }

        $stripeInvoice = new PaymentTranscation();
        $stripeInvoice->payment_method = 'paypal';
        $stripeInvoice->subscription_plan_id = $subscriptionPlan->id;
        $stripeInvoice->company_id = $company->id;
        $stripeInvoice->paid_on = \Carbon\Carbon::now()->format('Y-m-d');
        $stripeInvoice->next_payment_date = $nextPayDate->format('Y-m-d');
        $stripeInvoice->subscription_id = $billingPlan['id']; // // Store the plan id of stripe later on subscription we will update to subscrtion id
        $stripeInvoice->invoice_id = $product['id']; // Store the product id of stripe
        $stripeInvoice->transcation_id = $billingPlan['id']; // Store the plan id of stripe
        $stripeInvoice->total = $price;
        $stripeInvoice->plan_type = $type;
        $stripeInvoice->status = 'pending';
        $stripeInvoice->response_data = $billingPlan;
        $stripeInvoice->save();

        return ApiResponse::make('Success', $billingPlan);
    }

    public function payWithPaypalRecurrring(Request $request)
    {
        $company = company();

        try {
            $paypalOrderId = $request->paypal_order_id;
            $paypalSubscriptionID = $request->paypal_subscription_id;
            $orderDetails = $this->provider->showSubscriptionDetails($paypalSubscriptionID);

            if ($orderDetails && $orderDetails['plan_id']) {
                $paymentTranscation = PaymentTranscation::where('transcation_id', $orderDetails['plan_id'])
                    ->where('status', 'pending')->first();

                if ($paymentTranscation) {
                    $amount = $orderDetails['billing_info']['last_payment']['amount']['value'];
                    $payerId = $orderDetails['subscriber']['payer_id'];

                    $subscriptionPlan = SubscriptionPlan::where('id', $paymentTranscation->subscription_plan_id)->first();

                    $subscription = new Subscription();
                    $subscription->payment_method = 'paypal';
                    $subscription->company_id = $company->id;
                    $subscription->customer_id = $payerId;
                    $subscription->name = $subscriptionPlan->name;
                    $subscription->stripe_id = $paypalSubscriptionID;
                    $subscription->stripe_status = 'active';
                    $subscription->stripe_price = $amount;
                    $subscription->quantity = 1;
                    $subscription->plan_id = $paypalOrderId;
                    $subscription->plan_type = $paymentTranscation->plan_type;
                    $subscription->save();

                    $paymentTranscation->subscription_id = $paypalSubscriptionID;
                    $paymentTranscation->status = 'approved';
                    $paymentTranscation->save();

                    $company->payment_transcation_id = $paymentTranscation->id;
                    $company->subscription_plan_id = $subscriptionPlan->id;
                    $company->package_type = $paymentTranscation->plan_type;

                    // Set company status active
                    $company->status = 'active';
                    $company->licence_expire_on = null;

                    $company->save();

                    return ApiResponse::make('Success', [
                        'status' => 'success',
                        'message' => 'success'
                    ]);
                }
            }

            return ApiResponse::make('Success', [
                'status' => 'fail',
                'message' => 'No Data Found'
            ]);
        } catch (\Exception $th) {
            return ApiResponse::make('Success', [
                'status' => 'fail',
                'message' => $th->getMessage()
            ]);
        }
    }

    // public function payWithPaypalRecurrring(Request $requestObject)
    // {
    //     /** Get the payment ID before session clear **/
    //     $payment_id = Session::get('paypal_payment_id');
    //     $clientPayment =  PaypalInvoice::where('plan_id', $payment_id)->first();
    //     $company = $this->company;
    //     /** clear the session payment ID **/
    //     Session::forget('paypal_payment_id');

    //     if ($requestObject->get('success') == true && $requestObject->has('token') && $requestObject->get('success') != "false") {
    //         $token = $requestObject->get('token');
    //         $agreement = new Agreement();

    //         try {
    //             // ## Execute Agreement
    //             // Execute the agreement by passing in the token
    //             $agreement->execute($token, $this->_api_context);


    //             if ($agreement->getState() == 'Active' || $agreement->getState() == 'Pending') {

    //                 $this->cancelSubscription();
    //                 // Calculating next billing date
    //                 $today = Carbon::now();


    //                 $clientPayment->transaction_id = $agreement->getId();
    //                 if ($agreement->getState() == 'Active') {
    //                     $clientPayment->status = 'paid';
    //                 }
    //                 $clientPayment->paid_on = Carbon::now();
    //                 $clientPayment->save();

    //                 $company->subscription_plan_id = $clientPayment->subscription_plan_id;
    //                 $company->package_type = ($clientPayment->billing_frequency == 'year') ? 'annual' : 'monthly';
    //                 $company->status = 'active'; // Set company status active
    //                 $company->licence_expire_on = null;
    //                 $company->save();

    //                 if ($company->package_type == 'monthly') {
    //                     $today = $today->addMonth();
    //                 } else {
    //                     $today = $today->addYear();
    //                 }

    //                 $clientPayment->next_pay_date = $today->format('Y-m-d');
    //                 $clientPayment->save();

    //                 // TODO - Notification
    //                 //send superadmin notification
    //                 //                    $generatedBy = User::whereNull('company_id')->get();
    //                 //                    Notification::send($generatedBy, new CompanyUpdatedPlan($company, $clientPayment->package_id));

    //                 \Session::put('success', 'Payment successfully done');
    //                 return Redirect::route('admin.subscription-plan.index');
    //             }

    //             \Session::put('error', 'Payment failed');

    //             return Redirect::route('admin.subscription-plan.index');
    //         } catch (PayPalConnectionException $ex) {
    //             $errCode = $ex->getCode();
    //             $errData = json_decode($ex->getData());
    //             if ($errCode == 400 && $errData->name == 'INVALID_CURRENCY') {
    //                 \Session::put('error', $errData->message);
    //                 return Redirect::route('admin.subscription-plan.index');
    //             } elseif (\Config::get('app.debug')) {
    //                 \Session::put('error', 'Connection timeout');
    //                 return Redirect::route('admin.subscription-plan.index');
    //             } else {
    //                 \Session::put('error', 'Some error occur, sorry for inconvenient');
    //                 return Redirect::route('admin.subscription-plan.index');
    //             }
    //         }
    //     } else if ($requestObject->get('fail') == true || $requestObject->get('success') == "false") {
    //         \Session::put('error', 'Payment failed');

    //         return Redirect::route('admin.subscription-plan.index');
    //     } else {
    //         abort(403);
    //     }
    // }

    public function cancelSubscription()
    {
        $company = $this->company;
        $stripe = DB::table("stripe_invoices")
            ->join('subscription_plans', 'subscription_plans.id', 'stripe_invoices.subscription_plan_id')
            ->selectRaw('stripe_invoices.id , "Stripe" as method, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', $this->company->id);

        $allInvoices = DB::table("paypal_invoices")
            ->join('subscription_plans', 'subscription_plans.id', 'paypal_invoices.subscription_plan_id')
            ->selectRaw('paypal_invoices.id, "Paypal" as method, paypal_invoices.paid_on,paypal_invoices.next_pay_date')
            ->where('paypal_invoices.status', 'paid')
            ->whereNull('paypal_invoices.end_on')
            ->where('paypal_invoices.company_id', $this->company->id)
            ->union($stripe)
            ->get();

        $firstInvoice = $allInvoices->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->paid_on)->getTimestamp();
        })->first();

        if (!is_null($firstInvoice) && $firstInvoice->method == 'Paypal') {
            $credential = PaymentGatewaySettings::first();
            config(['paypal.settings.mode' => $credential->paypal_mode]);
            $paypal_conf = Config::get('paypal');
            $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
            $api_context->setConfig($paypal_conf['settings']);

            $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
                ->where('company_id', $this->company->id)->where('status', 'paid')->first();

            if ($paypalInvoice) {
                $agreementId = $paypalInvoice->transaction_id;
                $agreement = new Agreement();

                $agreement->setId($agreementId);
                $agreementStateDescriptor = new AgreementStateDescriptor();
                $agreementStateDescriptor->setNote("Cancel the agreement");

                try {
                    $agreement->cancel($agreementStateDescriptor, $api_context);
                    $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                    // Set subscription end date
                    $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                    $paypalInvoice->save();

                    $company->licence_expire_on = $paypalInvoice->end_on;
                    $company->save();
                } catch (\Exception $ex) {
                    // \Session::put('error','Some error occur, sorry for inconvenient');
                }
            }
        } elseif (!is_null($firstInvoice) && $firstInvoice->method == 'Stripe') {
            $this->setStripConfigs();

            $subscription = Subscription::where('company_id', $this->company->id)->whereNull('ends_at')->first();
            if ($subscription) {
                try {
                    $company->subscription('main')->cancel();

                    $company->licence_expire_on = $subscription->ends_at;
                    $company->save();
                } catch (\Exception $ex) {
                    //\Session::put('error','Some error occur, sorry for inconvenient');
                }
            }
        }
    }

    public function paypalInvoiceDownload($id)
    {
        //        header('Content-type: application/pdf');
        $this->invoice = PaypalInvoice::with(['company', 'currency', 'subscriptionPlan'])->findOrFail($id);
        //        $this->settings = $this->company;
        //        return view('invoices.'.$this->invoiceSetting->template, $this->data);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.invoices.paypal-invoice', $this->data);
        $filename = 'invoice-' . $this->invoice->paid_on->format($this->user->date_format);
        //       return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }
}

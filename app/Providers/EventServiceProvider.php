<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FrontProductCard;
use App\Models\FrontWebsiteSettings;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderShippingAddress;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\Role;
use App\Models\Settings;
use App\Models\StaffMember;
use App\Models\StockAdjustment;
use App\Models\StockHistory;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Variation;
use App\Models\Warehouse;
use App\Models\WarehouseHistory;
use App\Models\WarehouseStock;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\CurrencyObserver;
use App\Observers\CustomerObserver;
use App\Observers\CustomFieldObserver;
use App\Observers\ExpenseCategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\FrontProductCardObserver;
use App\Observers\FrontWebsiteSettingsObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderPaymentObserver;
use App\Observers\OrderShippingAddressObserver;
use App\Observers\PaymentModeObserver;
use App\Observers\PaymentObserver;
use App\Observers\ProductObserver;
use App\Observers\RoleObserver;
use App\Observers\SettingObserver;
use App\Observers\StaffMemberObserver;
use App\Observers\StockAdjustmentObserver;
use App\Observers\StockHistoryObserver;
use App\Observers\SupplierObserver;
use App\Observers\TaxObserver;
use App\Observers\UnitObserver;
use App\Observers\UserAddressObserver;
use App\Observers\UserObserver;
use App\Observers\VariationObserver;
use App\Observers\WarehouseHistoryObserver;
use App\Observers\WarehouseObserver;
use App\Observers\WarehouseStockObserver;
use App\SuperAdmin\Models\SuperAdmin;
use App\SuperAdmin\Observers\SuperAdminObserver;
use App\SuperAdmin\Observers\CompanyObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Don't run observer when
        // we run command using
        if (!app()->runningInConsole()) {
            $appType = app_type();

            Customer::observe(CustomerObserver::class);
            User::observe(UserObserver::class);
            StaffMember::observe(StaffMemberObserver::class);
            Supplier::observe(SupplierObserver::class);


            Brand::observe(BrandObserver::class);
            Category::observe(CategoryObserver::class);
            Product::observe(ProductObserver::class);
            Variation::observe(VariationObserver::class);
            Order::observe(OrderObserver::class);
            Payment::observe(PaymentObserver::class);
            OrderPayment::observe(OrderPaymentObserver::class);
            StockHistory::observe(StockHistoryObserver::class);
            StockAdjustment::observe(StockAdjustmentObserver::class);
            OrderShippingAddress::observe(OrderShippingAddressObserver::class);
            UserAddress::observe(UserAddressObserver::class);

            Expense::observe(ExpenseObserver::class);
            ExpenseCategory::observe(ExpenseCategoryObserver::class);

            FrontProductCard::observe(FrontProductCardObserver::class);
            FrontWebsiteSettings::observe(FrontWebsiteSettingsObserver::class);

            Warehouse::observe(WarehouseObserver::class);
            WarehouseStock::observe(WarehouseStockObserver::class);
            WarehouseHistory::observe(WarehouseHistoryObserver::class);


            Settings::observe(SettingObserver::class);
            Currency::observe(CurrencyObserver::class);
            CustomField::observe(CustomFieldObserver::class);
            PaymentMode::observe(PaymentModeObserver::class);
            Role::observe(RoleObserver::class);
            Tax::observe(TaxObserver::class);
            Unit::observe(UnitObserver::class);

            if ($appType == 'saas') {
                Company::observe(CompanyObserver::class);
                SuperAdmin::observe(SuperAdminObserver::class);
            }
        }
    }
}

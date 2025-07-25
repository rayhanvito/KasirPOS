<?php

use Examyou\RestAPI\Facades\ApiRoute;

// Admin Routes
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::get('all-langs', ['as' => 'api.extra.all-langs', 'uses' => 'AuthController@allEnabledLangs']);
    ApiRoute::get('pdf/{uniqueId}/{lang?}', ['as' => 'api.extra.pdf', 'uses' => 'AuthController@pdf']);
    ApiRoute::get('lang-trans', ['as' => 'api.extra.lang-trans', 'uses' => 'AuthController@langTrans']);
    ApiRoute::post('change-theme-mode', ['as' => 'api.extra.change-theme-mode', 'uses' => 'AuthController@changeThemeMode']);
    ApiRoute::get('default-walkin-customer', ['as' => 'api.extra.walkin-custome', 'uses' => 'AuthController@getDefaultWalkinCustomer']);

    // Check visibility of module according to subscription plan
    ApiRoute::post('check-subscription-module-visibility', ['as' => 'api.extra.check-subscription-module-visibility', 'uses' => 'AuthController@checkSubscriptionModuleVisibility']);
    ApiRoute::post('visible-subscription-modules', ['as' => 'api.extra.visible-subscription-modules', 'uses' => 'AuthController@visibleSubscriptionModules']);

    // Public Routes For Front
    ApiRoute::get('products/{product}', ['as' => 'api.products.show', 'uses' => 'ProductController@show']);
    ApiRoute::get('products', ['as' => 'api.products.index', 'uses' => 'ProductController@index']);
    ApiRoute::get('categories/{category}', ['as' => 'api.categories.show', 'uses' => 'CategoryController@show']);
    ApiRoute::get('categories', ['as' => 'api.categories.index', 'uses' => 'CategoryController@index']);
    ApiRoute::get('variations', ['as' => 'api.variations.index', 'uses' => 'VariationController@index']);
    ApiRoute::get('warehouses', ['as' => 'api.warehouses.index', 'uses' => 'WarehouseController@index']);
    ApiRoute::get('payment-modes', ['as' => 'api.payment-modes.index', 'uses' => 'PaymentModeController@index']);

    ApiRoute::group(['middleware' => ['api.auth.check']], function () {
        ApiRoute::post('dashboard', ['as' => 'api.extra.dashboard', 'uses' => 'AuthController@dashboard']);
        ApiRoute::post('upload-file', ['as' => 'api.extra.upload-file', 'uses' => 'AuthController@uploadFile']);
        ApiRoute::post('profile', ['as' => 'api.extra.profile', 'uses' => 'AuthController@profile']);
        ApiRoute::post('user', ['as' => 'api.extra.user', 'uses' => 'AuthController@user']);
        ApiRoute::get('timezones', ['as' => 'api.extra.user', 'uses' => 'AuthController@getAllTimezones']);
        ApiRoute::post('change-warehouse', ['as' => 'api.extra.change-warehouse', 'uses' => 'AuthController@changeAdminWarehouse']);
        ApiRoute::post('search-product', ['as' => 'api.extra.search-product', 'uses' => 'ProductController@searchProduct']);

        // Reports
        ApiRoute::post('reports/profit-loss', ['as' => 'api.reports.profit-loss', 'uses' => 'ReportController@profitLoss']);

        // Warehouse History
        ApiRoute::resource('warehouse-history', 'WarehouseHistoryController', ['as' => 'api', 'only' => ['index']]);
    });

    // Routes Accessable to thouse user who have permissions realted to route
    ApiRoute::group(['middleware' => ['api.permission.check', 'api.auth.check', 'license-expire']], function () {
        $options = [
            'as' => 'api'
        ];

        // Routes for Front Warehouse/Store
        // Routes Accessable to thouse user who have permissions realted to route
        ApiRoute::resource('product-cards', 'FrontProductCardsController', $options);
        ApiRoute::resource('front-settings', 'FrontWebsiteSettingsController', ['as' => 'api', 'only' => ['index', 'update']]);
        ApiRoute::post('product-lists/search-products', ['as' => 'api.extra.product-cards.search-products', 'uses' => 'FrontProductCardsController@searchProducts']);
        ApiRoute::post('online-orders/delivered/{id}', ['as' => 'api.online-orders.delivered', 'uses' => 'OnlineOrdersController@markAsDelivered']);
        ApiRoute::post('online-orders/change-status/{id}', ['as' => 'api.online-orders.change-status', 'uses' => 'OnlineOrdersController@changeOrderStatus']);
        ApiRoute::post('online-orders/confirm/{id}', ['as' => 'api.online-orders.confirm', 'uses' => 'OnlineOrdersController@confirmOrder']);
        ApiRoute::post('online-orders/cancel/{id}', ['as' => 'api.online-orders.cancel', 'uses' => 'OnlineOrdersController@cancelOrder']);
        ApiRoute::resource('online-orders', 'OnlineOrdersController', ['as' => 'api', 'only' => ['index']]);

        // Quotations
        ApiRoute::post('quotations/convert-to-sale/{id}', ['as' => 'api.quotations.convert-to-sale', 'uses' => 'QuotationController@convertToSale']);
        ApiRoute::resource('quotations', 'QuotationController', $options);

        //POS
        ApiRoute::post('pos/products', ['as' => 'api.pos.products', 'uses' => 'PosController@posProducts']);
        ApiRoute::post('pos/payment', ['as' => 'api.pos.payment', 'uses' => 'PosController@addPosPayment']);
        ApiRoute::post('pos/save', ['as' => 'api.pos.save', 'uses' => 'PosController@savePosPayments']);
        ApiRoute::post('product-warehouse-stock', ['as' => 'api.products.product-warehouse-stock', 'uses' => 'ProductController@getWarehouseStock']);

        ApiRoute::get('stock-alerts', ['as' => 'api.orders.items', 'uses' => 'AuthController@stockAlerts']);

        ApiRoute::post('user-invoices', ['as' => 'api.payments.user-invoices', 'uses' => 'PaymentController@userInvoices']);
        ApiRoute::post('customer-suppliers', ['as' => 'api.payments.customer-suppliers', 'uses' => 'PaymentController@customerSuppliers']);
        ApiRoute::resource('payments', 'PaymentInController', ['as' => 'api', 'only' => ['index']]);

        // Imports
        ApiRoute::post('brands/import', ['as' => 'api.brands.import', 'uses' => 'BrandController@import']);
        ApiRoute::post('categories/import', ['as' => 'api.categories.import', 'uses' => 'CategoryController@import']);
        ApiRoute::post('products/import', ['as' => 'api.products.import', 'uses' => 'ProductController@import']);
        ApiRoute::post('customers/import', ['as' => 'api.customers.import', 'uses' => 'CustomersController@import']);
        ApiRoute::post('suppliers/import', ['as' => 'api.suppliers.import', 'uses' => 'SuppliersController@import']);
        ApiRoute::post('users/import', ['as' => 'api.users.import', 'uses' => 'UsersController@import']);

        // Create Menu Update
        ApiRoute::post('companies/update-create-menu', ['as' => 'api.companies.update-create-menu', 'uses' => 'CompanyController@updateCreateMenu']);

        // Update waerhouse online_store_enabled
        ApiRoute::post('warehouses/update-online-store-status', ['as' => 'api.warehouses.update-online-store-status', 'uses' => 'WarehouseController@updateOnlineStoreStatus']);

        // Payments
        ApiRoute::resource('payment-out', 'PaymentOutController', $options);
        ApiRoute::resource('payment-in', 'PaymentInController', $options);

        // Products
        ApiRoute::post('products/check-variants', ['as' => 'api.products.check-variants', 'uses' => 'ProductController@checkProductVariant']);
        ApiRoute::resource('products', 'ProductController', ['as' => 'api', 'except' => ['index']]);

        ApiRoute::resource('brands', 'BrandController', $options);
        ApiRoute::resource('categories', 'CategoryController', ['as' => 'api', 'except' => ['index', 'show']]);
        ApiRoute::resource('variations', 'VariationController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('order-payments', 'OrderPaymentController', ['as' => 'api', 'only' => ['index', 'store']]);
        ApiRoute::resource('payment-modes', 'PaymentModeController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('units', 'UnitController', $options);
        ApiRoute::resource('taxes', 'TaxController', $options);
        ApiRoute::resource('expenses', 'ExpenseController', $options);
        ApiRoute::resource('expense-categories', 'ExpenseCategoryController', $options);
        ApiRoute::resource('users', 'UsersController', $options);
        ApiRoute::resource('customers', 'CustomersController', $options);
        ApiRoute::resource('suppliers', 'SuppliersController', $options);
        ApiRoute::resource('companies', 'CompanyController', ['as' => 'api', 'only' => ['update']]);
        ApiRoute::resource('permissions', 'PermissionController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('stock-history', 'StockHistoryController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('order-items', 'OrderItemController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('roles', 'RolesController', $options);
        ApiRoute::resource('warehouses', 'WarehouseController',  ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('custom-fields', 'CustomFieldController', $options);
        ApiRoute::resource('stock-adjustments', 'StockAdjustmentController', $options);
        ApiRoute::resource('purchases', 'PurchaseController', $options);
        ApiRoute::resource('purchase-returns', 'PurchaseReturnsController', $options);
        ApiRoute::resource('stock-transfers', 'StockTransferController', $options);
        ApiRoute::resource('sales', 'SalesController', $options);
        ApiRoute::resource('sales-returns', 'SalesReturnsController', $options);
    });
});

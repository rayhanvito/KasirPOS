<?php

use Examyou\RestAPI\Facades\ApiRoute;

// Front Store/Warehouse Routes
ApiRoute::group(['prefix' => 'front', 'namespace' => 'App\Http\Controllers\Api\Front'], function () {
    ApiRoute::get('app/{warehouse}', ['as' => 'api.front.app', 'uses' => 'HomePageController@app']);
    ApiRoute::get('homepage/{warehouse}', ['as' => 'api.front.homepage', 'uses' => 'HomePageController@homepage']);
    ApiRoute::post('categories', ['as' => 'api.front.categories', 'uses' => 'HomePageController@categories']);
    ApiRoute::post('category-by-slug/{slug}', ['as' => 'api.front.category-by-slug', 'uses' => 'HomePageController@categoryBySlug']);

    ApiRoute::post('login', ['as' => 'api.front.login', 'uses' => 'HomePageController@login']);
    ApiRoute::post('signup', ['as' => 'api.front.signup', 'uses' => 'HomePageController@signup']);
    ApiRoute::post('refresh-token', ['as' => 'api.front.refresh-token', 'uses' => 'HomePageController@refreshToken']);
    ApiRoute::post('logout', ['as' => 'api.front.logout', 'uses' => 'HomePageController@logout']);

    ApiRoute::group(['middleware' => ['api.front.check']], function () {
        ApiRoute::post('profile', ['as' => 'api.front.profile', 'uses' => 'HomePageController@profile']);
        ApiRoute::post('user', ['as' => 'api.front.user', 'uses' => 'HomePageController@user']);
        ApiRoute::post('upload-file', ['as' => 'api.front.upload-file', 'uses' => 'HomePageController@uploadFile']);

        ApiRoute::group(['prefix' => 'self'], function () {
            // Dashboard Routes
            ApiRoute::post('dashboard', ['as' => 'api.front.self.dashboard', 'uses' => 'DashboardController@dashboard']);
            ApiRoute::post('orders', ['as' => 'api.front.self.orders', 'uses' => 'DashboardController@orders']);
            ApiRoute::post('checkout-orders/{warehouse}', ['as' => 'api.front.self.checkout-orders', 'uses' => 'DashboardController@checkoutOrders']);
            ApiRoute::post('checkout-success/{orderUniqueId}', ['as' => 'api.front.self.checkout-success', 'uses' => 'DashboardController@checkoutSuccess']);
            ApiRoute::post('cancel-order/{orderUniqueId}', ['as' => 'api.front.self.cancel-order', 'uses' => 'DashboardController@cancelOrder']);


            ApiRoute::resource('address', 'UserAddressController', ['as' => 'api.front']);
        });
    });
});

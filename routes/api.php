<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});


Route::get('make-store-customer', 'Api\OrderController@makeStoreCustomer');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:customer')->get('/customer', function (Request $request) {
    dd($request->all());

    Route::get('list', 'Api\CategoryController@list');
});

Route::get('test', 'Customer_Api\BusinessTypeController@test');
Route::post('test-pg', 'Customer_Api\StoreOrderController@pgTest');


Route::get('video/list', 'Api\StoreController@VideoList');
Route::get('dboy/list', 'Delivery_Api\DeliveryBoyController@dboy');

Route::get('store-video/list', 'Api\StoreController@storeVideoList');
Route::get('customer-video/list', 'Api\StoreController@customerVideoList');
Route::get('delivery-boy-video/list', 'Api\StoreController@deliveryBoyVideoList');


//category list
Route::get('category/list', 'Api\CategoryController@list');
//country list
Route::get('country/list', 'Api\LocationController@countryList');
//state list
Route::get('state/list', 'Api\LocationController@stateList');
//district list
Route::get('district/list', 'Api\LocationController@districtList');
//business type list
Route::get('business-type/list', 'Api\BusinessTypeController@typeList');
//town list
Route::get('town/list', 'Api\TownController@townList');
//check store mobile number unqiue
Route::get('store/check/mobile-unique', 'Api\StoreController@mobCheck');
//check store name unqiue
Route::get('store/check/name-unique', 'Api\StoreController@nameCheck');
//save Store
Route::post('store/save', 'Api\StoreController@saveStore');
//Store otpcheck 
Route::get('store/otp-verify', 'Api\StoreController@verifyOtp');
//Store resend OTP
Route::get('store/resend-otp', 'Api\StoreController@resendOtp');
//Login Store
Route::post('store/login', 'Api\StoreController@loginStore');
Route::get('store/login-status', 'Api\StoreController@loginStoreStatus');
Route::get('store/logout-all-device', 'Api\StoreController@logoutAllDevice');

//Forgot Password - Store
Route::get('store/forgot-password/verify_mobile', 'Api\StoreController@FpverifyMobile');
Route::get('store/forgot-password/verify_otp', 'Api\StoreController@FpverifyOTP');
Route::post('store/forgot-password/reset-password', 'Api\StoreController@resetPassword');

Route::get('store/switch-status', 'Api\StoreController@onlineStatus');
//Online Status
Route::get('store/get-online-status', 'Api\StoreController@getOnlineStatus');

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('store/logout', 'Api\StoreController@logout');
    Route::get('store/logout-test', 'Api\StoreController@logoutTest');
    Route::get('store/logout-other-device', 'Api\StoreController@logoutOtherDevice');
    Route::post('store/check-login-status', 'Api\StoreController@checkLoginStatus');

    Route::get('store/get-login-online-status', 'Api\StoreController@getLoginOnlineStatus');
    // Route::get('store/get-login-online-status2', 'Api\StoreController@getLoginOnlineStatus2');
    //  Route::get('store/get-login-online-status3', 'Api\StoreController@getLoginOnlineStatus2');
});
//Route::get('store/get-login-online-status3', 'Api\StoreController@getLoginOnlineStatus2');


//-----------------------  STORE  ---------------------------
// 'middleware' => 'auth:api'
Route::group(['prefix' => 'store'], function () {

    // PRODUCT API

    // Route::post('product/add','Api\ProductController@addProduct');
    Route::get('product/list', 'Api\ProductController@list'); //->middleware('auth:api');
    Route::get('product/list-by-category', 'Api\ProductController@listByCategory');
    Route::get('product/edit', 'Api\ProductController@editProduct');
    Route::post('product/update', 'Api\ProductController@updateProduct');
    Route::get('product/view', 'Api\ProductController@viewProduct');
    Route::get('product/remove', 'Api\ProductController@removeProduct');
    Route::get('product-image/delete', 'Api\ProductController@removeProductImage');

    Route::post('product/add', 'Api\ProductController@addProductn');
    Route::post('product-vartiants/add', 'Api\ProductController@addProductVariants');


    // TAX 
    Route::get('tax/list', 'Api\ProductController@listTax');

    // PRODUCT TYPES
    Route::get('product-type/list', 'Api\ProductController@listProductType');

    // SERVICE TYPES
    Route::get('service-type/list', 'Api\ProductController@listServiceType');

    // COLOR
    Route::get('color/list', 'Api\ProductController@listColor');

    // ATTRIBUTE GROUP
    Route::get('attribute-group/list', 'Api\ProductController@listAttributeGroup');

    // ATTRIBUTE VALUE
    Route::get('attribute-value/list', 'Api\ProductController@listAttributeValue');

    // PRODUCT CATEGORY
    Route::get('product-category/list', 'Api\ProductController@listProductCategory');

    // PRODUCT CATEGORY
    Route::get('product-sub-category/list', 'Api\ProductController@listProductSubCategory');

    // GLOBAL PRODUCT BASED ON VENDOR
    Route::get('global-products/list', 'Api\ProductController@listGlobalProductsByVendor');

    // VENDOR
    Route::get('vendor/list', 'Api\ProductController@listVendor');

    //PRODUCT VARIANTS
    Route::get('product-vartiants/list', 'Api\ProductController@listProductVariants');
    Route::get('product-vartiants/single', 'Api\ProductController@singleVariant');
    Route::get('product-vartiant/remove', 'Api\ProductController@removeVariant');
    Route::get('product-vartiant-attr/list', 'Api\ProductController@listVariantAttr');
    Route::post('product-vartiant-attr/add', 'Api\ProductController@addVariantAttr');
    Route::get('product-vartiant-attr/remove', 'Api\ProductController@removeVariantAttr');

    Route::get('product/base-image', 'Api\ProductController@setDefaultImage');

    Route::get('product-code', 'Api\ProductController@productExists');

    //----------- INVENTORY API ----------------
    Route::get('product/inventory/list', 'Api\InventoryController@listInventoryProducts');
    Route::post('product/inventory/update-stock', 'Api\InventoryController@updateInventory');
    Route::get('product/inventory/reset-stock', 'Api\InventoryController@resetStock');


    //----------- ORDER API ----------------
    Route::get('order/list', 'Api\OrderController@listOrders'); // order list
    Route::get('order/view', 'Api\OrderController@viewOrder'); // show single order
    Route::get('delivery-boys/list', 'Api\OrderController@listDeliveryBoys'); // delivery boys
    Route::get('delivery-boys/active-list', 'Api\OrderController@activeDelievryBoysList'); // old delivery boys

    Route::get('order-status/list', 'Api\OrderController@listOrderStatus'); // order status
    Route::post('order/update', 'Api\OrderController@updateOrder'); // order update
    Route::get('order/invoice', 'Api\OrderController@orderInvoice'); // order 
    Route::post('order/assign-delivery-boy', 'Api\OrderController@assignDeliveryBoy'); // order 

    //POS
    Route::get('customer/list', 'Api\PosController@listCustomers'); // customer list
    Route::post('pos-order/save', 'Api\PosController@saveOrder');  // save order
    Route::get('pos-product/list', 'Api\PosController@listProducts'); // list products

    // COUPON
    Route::get('coupon/list', 'Api\CouponController@listCoupon'); //list by store
    Route::get('coupon-filter/list', 'Api\CouponController@listFilterCoupon'); // filter by status
    Route::post('coupon/save', 'Api\CouponController@saveCoupon'); // save 
    Route::get('coupon-type/list', 'Api\CouponController@listCouponType'); // coupon type
    Route::get('discount-type/list', 'Api\CouponController@listDiscountType'); // discount type
    Route::get('coupon/edit', 'Api\CouponController@editCoupon'); //edit by store and  coupon id
    Route::post('coupon/update', 'Api\CouponController@updateCoupon'); //update by store and coupon id
    Route::get('coupon/delete', 'Api\CouponController@deleteCoupon'); //delete

    //TIME SLOT
    Route::get('time-slot/list', 'Api\TimeSlotController@listTimeSlots'); //list time slots
    Route::post('time-slot/update', 'Api\TimeSlotController@updateTimeSlots'); // update

    //DISPUTES
    Route::get('dispute/list', 'Api\DisputeController@listDispute'); // list all in store
    Route::get('dispute/view', 'Api\DisputeController@viewDispute'); // view single dispute
    Route::post('dispute/update', 'Api\DisputeController@updateDispute'); // update single dispute

    //STORE SETTINGS
    Route::get('store-settings/list', 'Api\StoreSettingsController@listDefaultSettings');
    Route::post('store-settings/update', 'Api\StoreSettingsController@updateSettings');

    Route::get('remove-banner', 'Api\StoreSettingsController@removeBanner');

    //WORKING DAYS
    Route::get('working-days/list', 'Api\StoreSettingsController@listWorkingDays');
    Route::post('working-days/update', 'Api\StoreSettingsController@updateWorkingDays');

    //STORE PROFILE
    Route::get('store-info/list', 'Api\StoreSettingsController@listStoreInfo');
    Route::post('store-info/update', 'Api\StoreSettingsController@updateStoreInfo');
    Route::post('password/update', 'Api\StoreSettingsController@updatePassword');
    Route::post('add-bank-details', 'Api\StoreSettingsController@updateBankDetails');

    //DELIVERY BOYS
    Route::get('delivery-boys/list-by-status', 'Api\OrderController@listDeliveryBoysByStatus');

    //DASHBOARD
    Route::get('dashboard', 'Api\StoreSettingsController@dashboard');


    //GLOBAL PRODUCT
    Route::get('global-product/list', 'Api\ProductController@listGlobalProduct');
    Route::get('global-product/view', 'Api\ProductController@viewGlobalProduct');
    Route::post('global-product/convert', 'Api\ProductController@convertGlobalProduct');

     //RESTORE PRODUCTS
     Route::get('restore-product/list', 'Api\ProductController@restoreDeletedProduct');
     Route::get('restore-product/save', 'Api\ProductController@updaterestoreDeletedProduct');

    //REPORTS 
    Route::get('product-wise-report', 'Api\ProductController@showReport');
    Route::get('store-visit-report', 'Api\ProductController@showStoreVisitReport');

    // Route::get('store-visit-report','Api\ProductController@showStoreVisitReport'); 
    // Route::get('store-visit-report','Api\ProductController@showStoreVisitReport'); 

    Route::get('sales-report', 'Api\StoreController@salesReport');
    Route::get('online-sales-report', 'Api\StoreController@salesOnlineReport');
    Route::get('offline-sales-report', 'Api\StoreController@salesOfflineReport');

    Route::get('inventory-report', 'Api\StoreController@inventoryReport');
    Route::get('out-of-stock-report', 'Api\StoreController@outOffStockReport');

    Route::get('payment-report', 'Api\StoreController@paymentReport');
    Route::get('delivery-report', 'Api\StoreController@deliveryReport');

    Route::get('product-name-list', 'Api\StoreController@listProducts');

    Route::get('incoming-payment-report', 'Api\StoreController@incomingPaymentReport');
});





//testing token

Route::group(['prefix' => 'store', 'middleware' => 'auth:api'], function () {

    Route::get('dashboard/test', 'Api\StoreSettingsController@dashboard');
});






//============================ CUSTOMER =============================

//login
Route::post('customer/login', 'Customer_Api\CustomerController@loginCustomer');

//mobile unique check
Route::get('customer/check/mobile-unique', 'Customer_Api\CustomerController@mobUniqueCheck');

//email unique check
Route::get('customer/check/email-unique', 'Customer_Api\CustomerController@emailUniqueCheck');

//store customer
Route::post('customer/save', 'Customer_Api\CustomerController@saveCustomer');

//customer otp verify 
Route::get('customer/otp-verify', 'Customer_Api\CustomerController@verifyOtp');

//customer resend otp
Route::get('customer/resend-otp', 'Customer_Api\CustomerController@resendOtp');

//Forgot Password - Customer
Route::get('customer/forgot-password/verify_mobile', 'Customer_Api\CustomerController@FpverifyMobile');
Route::get('customer/forgot-password/verify_otp', 'Customer_Api\CustomerController@FpverifyOTP');
Route::post('customer/forgot-password/reset-password', 'Customer_Api\CustomerController@resetPassword');


//HOME PAGE STORE LISTING 
Route::get('customer/store-categories', 'Customer_Api\ProductController@listStoreProductCategory');
Route::get('customer/offer-products', 'Customer_Api\ProductController@OfferProductes');


Route::get('customer/', 'Customer_Api\ProductController@storeData');
Route::get('customer/store-offer-products', 'Customer_Api\ProductController@storeOfferProducts');
Route::get('customer/store-products', 'Customer_Api\ProductController@storeProducts');
Route::get('customer/store-products-by-categories', 'Customer_Api\ProductController@storeProductsByCat');
Route::get('customer/list-stores', 'Customer_Api\ProductController@listStores');
Route::get('customer/recently-visited-products', 'Customer_Api\ProductController@RecentlyVisited');

Route::get('customer/store-products-by-name', 'Customer_Api\ProductController@storeProductsByName');
Route::get('customer/store-products-by-store-name', 'Customer_Api\ProductController@storeProductsByStoreName');

Route::get('customer/store-data', 'Customer_Api\ProductController@storeData');


Route::post('customer/add-to-cart', 'Customer_Api\PurchaseController@addToCart');
Route::get('customer/cart-items', 'Customer_Api\PurchaseController@cartItems');
Route::get('customer/remove-cart-item', 'Customer_Api\PurchaseController@removeCartItems');
Route::post('customer/update-qty', 'Customer_Api\PurchaseController@updateQty');
Route::post('customer/address-edit', 'Customer_Api\PurchaseController@editAddress');

Route::post('customer/add-address', 'Customer_Api\PurchaseController@addAddress');
// ALTER TABLE `trn_store_customers` ADD `address_2` TEXT NULL AFTER `customer_address`;

Route::post('customer/update-amount', 'Customer_Api\PurchaseController@upateAmount');
Route::get('customer/validate-coupon', 'Customer_Api\PurchaseController@validateCoupon');
Route::get('customer/time-slots', 'Customer_Api\StoreOrderController@storeTimeSlots');
Route::get('customer/payment-types', 'Customer_Api\StoreOrderController@listPaymentType');
Route::post('customer/save-order', 'Customer_Api\StoreOrderController@saveOrder');
Route::post('customer/save-order-service', 'Customer_Api\StoreOrderController@saveOrderService');

//raise an issue
//ALTER TABLE `mst__issues` ADD `issue_type_id` BIGINT NULL DEFAULT '0' AFTER `issue_id`;
Route::get('customer/issue-types', 'Customer_Api\StoreOrderController@issueTypes');
Route::get('customer/issues', 'Customer_Api\StoreOrderController@issues');

//ALTER TABLE `mst_disputes` ADD `order_item_id` BIGINT NULL DEFAULT '0' AFTER `order_id`;
Route::post('customer/upload-issue', 'Customer_Api\StoreOrderController@uploadIssue');

Route::get('customer/all-categories', 'Customer_Api\ProductController@listAllProductCategory');
Route::get('customer/most-visited-products', 'Customer_Api\ProductController@mostVisitedProducts');


// customer order
Route::get('customer/order-history', 'Customer_Api\StoreOrderController@orderHistory');
Route::get('customer/order-view', 'Customer_Api\StoreOrderController@viewOrder');
Route::get('customer/cancel-order', 'Customer_Api\StoreOrderController@cancelOrder');
Route::post('customer/product-stock-status', 'Customer_Api\StoreOrderController@stockAvailability');

// profile
Route::get('customer/customer-info', 'Customer_Api\CustomerController@viewInfo');
Route::post('customer/add-address', 'Customer_Api\CustomerController@addAddress');
Route::post('customer/edit-address', 'Customer_Api\CustomerController@editAddress');
Route::get('customer/remove-address', 'Customer_Api\CustomerController@removeAddress');
Route::get('customer/view-address', 'Customer_Api\CustomerController@viewAddress');
Route::post('customer/update-profile', 'Customer_Api\CustomerController@updateProfile');
Route::post('customer/update-password', 'Customer_Api\CustomerController@updatePassword');
//ALTER TABLE `trn_store_customers` ADD `gender` VARCHAR(20) NULL AFTER `customer_address`, ADD `dob` DATE NULL AFTER `gender`;


Route::get('customer/reward-point', 'Customer_Api\CustomerController@totalRewardList');
Route::get('customer/reward-point-count', 'Customer_Api\CustomerController@totalRewardCount');

//PRODUCT DETAIL
Route::post('customer/add-review', 'Customer_Api\ProductController@addReview');
Route::get('customer/list-review', 'Customer_Api\ProductController@listReview');
Route::get('customer/product-detail', 'Customer_Api\ProductController@singleProductVariant');


//visit count
Route::get('store-visit', 'Customer_Api\VisitController@storeVisitByCustomer');
Route::get('business-type-visit', 'Customer_Api\VisitController@businessTypeVisitByCustomer');
Route::get('category-visit', 'Customer_Api\VisitController@categoryVisitByCustomer');
Route::post('cart/products-removed', 'Customer_Api\VisitController@productRemoved');
Route::get('products-visited', 'Customer_Api\VisitController@productsVisited');


//business-type/list - home page
Route::get('customer/business-type/list', 'Customer_Api\BusinessTypeController@typeList');

Route::get('customer/business-type/offer-products', 'Customer_Api\BusinessTypeController@OfferProducts');

Route::get('customer/business-type/recently-visited-products', 'Customer_Api\BusinessTypeController@RecentlyVisited');
Route::get('customer/business-type/store-list', 'Customer_Api\BusinessTypeController@storeList');


Route::group(['middleware' => 'auth:api-customer'], function () {

    Route::get('customer/logout', 'Customer_Api\CustomerController@logout');
});



//customer home
Route::get('customer/home', 'Customer_Api\ProductController@homePage');
Route::get('customer/home2', 'Customer_Api\BusinessTypeController@homePage');
Route::get('customer/business-type/home', 'Customer_Api\BusinessTypeController@BTHomePage');
Route::get('customer/store-home', 'Customer_Api\ProductController@homePageStore');
Route::get('customer/category-home', 'Customer_Api\ProductController@homePageCategory');
Route::get('customer/sub-category-home', 'Customer_Api\ProductController@homePageSubCategory');

Route::get('customer/view-product', 'Customer_Api\ProductController@viewProduct');
Route::get('customer/view-product-attr', 'Customer_Api\ProductController@viewProductAttr'); //1
Route::get('customer/view-base-product', 'Customer_Api\ProductController@viewBaseProduct'); //2
Route::get('customer/view-product-popup', 'Customer_Api\ProductController@viewProductPopup');

Route::get('customer/base-product/variants', 'Customer_Api\ProductController@viewBaseProductVariants');


Route::get('customer/search-product', 'Customer_Api\ProductController@searchProduct');
Route::get('customer/search-store', 'Customer_Api\ProductController@searchStore');

Route::get('customer/cart-page', 'Customer_Api\ProductController@viewCart');

Route::get('customer/wallet-page', 'Customer_Api\ProductController@walletPage');
Route::get('customer/raise-issues', 'Customer_Api\ProductController@raiseIssuesPage');


Route::get('customer/address-coupon-list', 'Customer_Api\ProductController@listCouponAndAddress');
Route::get('customer/checkout-page', 'Customer_Api\ProductController@checkOutPage');

Route::get('customer/reduce-reward-point-2', 'Customer_Api\ProductController@reduceRewardPoint');
Route::get('customer/reduce-reward-point', 'Customer_Api\PurchaseController@reduceRewardPoint');

Route::post('customer/share-feedback', 'Customer_Api\ProductController@shareFeedback');

Route::post('customer/payment-response', 'Customer_Api\ProductController@paymentResponse');

Route::get('test-api', 'Customer_Api\ProductController@testApi');




//============================ Delivery =============================
// Delivery_Api

Route::group(['middleware' => 'auth:api-delivery'], function () {


Route::get('delivery-boy/logout', 'Delivery_Api\DeliveryBoyController@logout');

});

Route::post('delivery-boy/login', 'Delivery_Api\DeliveryBoyController@loginDelivery');

//Forgot Password - dboy
Route::get('delivery-boy/forgot-password/verify_mobile', 'Delivery_Api\DeliveryBoyController@FpverifyMobile');
Route::get('delivery-boy/forgot-password/verify_otp', 'Delivery_Api\DeliveryBoyController@FpverifyOTP');
Route::post('delivery-boy/forgot-password/reset-password', 'Delivery_Api\DeliveryBoyController@resetPassword');
Route::get('delivery-boy/resend-otp', 'Delivery_Api\DeliveryBoyController@resendOtp');



Route::get('delivery-boy/assigned-orders', 'Delivery_Api\DeliveryBoyOrderController@assignedOrders');
Route::get('delivery-boy/accept-order', 'Delivery_Api\DeliveryBoyOrderController@orderAcceptance');
Route::get('delivery-boy/view-order', 'Delivery_Api\DeliveryBoyOrderController@viewOrder');

Route::get('delivery-boy/completed-orders', 'Delivery_Api\DeliveryBoyOrderController@completedOrders');

Route::get('delivery-boy/view-profile', 'Delivery_Api\DeliveryBoyController@viewProfile');
Route::post('delivery-boy/update-profile', 'Delivery_Api\DeliveryBoyController@updateProfile');
Route::post('delivery-boy/update-password', 'Delivery_Api\DeliveryBoyController@updatePassword');

Route::get('delivery-boy/view-order-items', 'Delivery_Api\DeliveryBoyOrderController@viewOrderItems');


Route::post('delivery-boy/order/update', 'Delivery_Api\DeliveryBoyOrderController@updateOrderDeliveryCheckStatue'); // order update
Route::post('delivery-boy/order/status-update', 'Delivery_Api\DeliveryBoyOrderController@updateOrderStatus'); // order update


Route::get('delivery-boy/location-update', 'Delivery_Api\DeliveryBoyController@updateLoc');

Route::get('delivery-boy/delivery-report', 'Delivery_Api\DeliveryBoyController@deliveryReport');


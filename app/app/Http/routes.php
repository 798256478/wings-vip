<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/getoldmysql', 'Data_migrationController@basic_table');
Route::get('/getoldmongo', 'Data_migrationController@index');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web', 'test']], function () {
    Route::any('/wechatoauth2', 'wechat\OAuth2Controller@callback');
    Route::get('/test', 'CommandController@test');
    Route::get('/reset', 'CommandController@reset');
    Route::get('/update', 'CommandController@update');
    Route::any('/WXOrderNoticeListen', 'wechat\OrderNoticeController@noticeListen');
    Route::any('/WechatEventListen', 'wechat\WechatEventController@dispatcher');
    Route::get('/WechatMenu', 'wechat\WechatEventController@setMenu');

});

Route::group(['prefix' => 'wechat', 'namespace'=>'Wechat', 'middleware' => ['web','wechat.auth']], function () {

    //TODO:李广
    Route::get('/', 'CardController@showCard');//首页
    Route::get('/balance', 'CardController@showBalance');//余额页
    Route::get('/bonus', 'CardController@showBonus');//积分页
    Route::get('/records', 'CardController@showRecords');//交易记录页
    Route::get('/member_info', 'CardController@showMemberInfo');//会员信息页
    Route::get('/redeem_code', 'CardController@showRedeemCode');//兑换页
    Route::get('/attendance', 'CardController@showAttendance');//签到页

    Route::get('/qrCode', 'CardController@showQrCode');//签到页
    //TODO:申赵柯
    Route::group(['prefix' => '/member_info'], function() {
        Route::get('/phone', 'MemberInfoController@editPhone');//手机号编辑页
        Route::put('/phone', 'MemberInfoController@putPhone');//手机号更新
        Route::get('/verify', 'MemberInfoController@showVerify');//口令验证页
        Route::post('/verifyWord', 'MemberInfoController@verifyWord');//验证口令
        Route::get('/word', 'MemberInfoController@editWord');//口令编辑页
        Route::put('/word', 'MemberInfoController@putWord');//口令更新
    });

    //TODO:申赵柯
    Route::group(['prefix' => '/address'], function() {
        Route::get('/', 'AddressController@showAddresses');//地址列表
        Route::get('/new', 'AddressController@newAddress');//地址新建页
        Route::get('/{id}', 'AddressController@editAddress');//地址编辑页
        Route::post('/', 'AddressController@postAddress');//新建地址
        Route::put('/{id}', 'AddressController@putAddress');//更新地址
        Route::delete('/{id}', 'AddressController@deletedAddress');//删除地址
    });

    //TODO:李广义
    Route::group(['prefix' => '/mall'], function() {
        Route::get('/', 'MallController@showMall');//商城首页
        Route::get('/shop/{id?}', 'MallController@showShop');//店铺页
        Route::get('/item/{id?}', 'MallController@showItem');//商品详情页
        Route::get('/suit/{id?}', 'MallController@showSuit');//套餐详情页
    });

    //TODO:朱贝鸽
    Route::group(['prefix' => '/order'], function() {
        Route::get('/', 'OrderController@showOrders');//订单列表
        Route::get('/new/{parameter?}', 'OrderController@newOrder');//结算页
        Route::post('/', 'OrderController@postGoodsOrder');//新建商品订单
        Route::post('/balance', 'OrderController@postBalanceOrder');//新建储值订单
        Route::get('/qrCodePay', 'OrderController@showQrCodePay');
        Route::post('/qrCodePay', 'OrderController@postQrCodePay');
//        Route::get('/newSuit/{suitId}/{amount}', 'OrderController@newSuitOrder');//新建套装订单
        Route::get('/{id}', 'OrderController@showOrder');//订单详情
        Route::get('/tickets/choose', 'OrderController@showTickets');//显示可用的券
        Route::get('/paySuccess/{id}','OrderController@showPaySuccess');//支付成功（订单ID）
        Route::get('/refund/new/{orderId}/{orderDetailId?}','RefundController@newRefund');//新建退款
        Route::post('/refund','RefundController@postRefund');//新建退款
    });

    Route::group(['prefix' => '/api'], function() {
        //TODO:李广义
        Route::post('/redeem_code', 'CardController@redeemCode');//使用兑换码
        Route::post('/receive-notice', 'CardController@receiveNotice');//阅读通知
        //TODO:申赵柯
        Route::put('/member_info', 'MemberInfoController@putMemberInfo');//更新会员信息
        Route::post('/sendSms', 'MemberInfoController@sendSms');//发送验证码
        Route::post('/order/choosePayType', 'OrderController@choosePayType');//选择付款方式
        //TODO:朱贝鸽
        Route::post('/attendance', 'CardController@postAttendance');//签到
        Route::delete('/{id}', 'OrderController@deleteOrder');//取消订单
        Route::delete('/refund/{id}','RefundController@deleteRefund');//取消退款
        //TODO:李广义
        Route::put('/putCartData','MallController@putCartData');//更新购物车


    });

    Route::group(['prefix' => '/health'], function() {
        Route::get('/', 'health\ExperimentDataController@index');
        Route::get('/info/{experiment_data_id}', 'health\ExperimentDataController@getInfo');
        Route::match(['get','post'], '/detection/new', 'health\CustomerController@addUserInfo');
        Route::get('/detection/userInfo/{barcode_id}/{experiment_data_id}', 'health\CustomerController@userInfo');
        Route::post('detection/save_info/{id}', 'health\CustomerController@saveInfo');
        Route::post( '/detection/saveUserInfo', 'health\CustomerController@saveUserInfo');
        Route::get('/detection/phone/{barcode_id}/{experiment_data_id}', 'health\CustomerController@editPhone');    //基因检测修改个人信息手机号编辑页
        Route::put('/detection/phone', 'health\CustomerController@putPhone');      //基因检测修改个人信息手机号更新
        Route::get('/detection/riskdatas/{experiment_data_id}', 'health\RecordController@riskDatas');
        Route::get('/detection/report/{experiment_data_id}', 'health\RecordController@index');
        Route::get('/detection/reportDetail/{experiment_data_id}/{id}', 'health\RecordController@reportDetail');
        Route::get('/detection/progress/{experiment_data_id}', 'health\ProgressController@progress');
        Route::get('/detection/questionnaire/{barcode_id}/{experiment_data_id}','health\QuestionnaireController@showQuestionnaire');
        Route::post('/detection/questionnaire/{barcode_id}', 'health\QuestionnaireController@saveQuestionnaireAnswer');
        Route::post( '/detection/saveUserInfo', 'health\CustomerController@saveUserInfo');
        Route::post( '/detection/detectionSendSms', 'health\CustomerController@detectionSendSms');  //基因检测修改个人信息发送验证码
        Route::get('/detection/reservation/{experiment_data_id}', 'health\ReservationController@showReservation');   //预约页
        Route::post('detection/saveReserveInfo', 'health\ReservationController@postReservation');      //新建预约
    });
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
|
*/

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) {

    //$api->get('/api/example', 'App\Http\Controllers\Api\ExampleController@index');

    $api->group(['prefix' => '/api'], function($api){
        $api->post('/login', 'App\Http\Controllers\Api\AuthController@login');
        $api->get('/login', 'App\Http\Controllers\Api\AuthController@login');
        $api->get('/setting/{key}', 'App\Http\Controllers\Api\SettingController@getSetting');
        $api->get('/settingModule', 'App\Http\Controllers\Api\SettingController@getEnableModules');
        $api->get('/setting_all', 'App\Http\Controllers\Api\SettingController@getAll');
        $api->get('/short_experiments', 'App\Http\Controllers\Api\Health\ExperimentController@get_short_experiments');
        $api->get('/records/{barcode}/{experimentid}/{pageindex}/{pagesize}', 'App\Http\Controllers\Api\Health\RecordController@getRcords');
        $api->get('/record/{id}', 'App\Http\Controllers\Api\Health\RecordController@get_by_id');


        $api->get('/getReportData/{code}','App\Http\Controllers\Api\Health\RecordController@getReportData');
        $api->get('/getReportDetail/{code}/{projectId}','App\Http\Controllers\Api\Health\RecordController@getReportDetail');

        $api->post('/systemsync/memberevent','App\Http\Controllers\Api\Yuda\SystemController@distribute');//对裕达的接口
        $api->get('/systemsync/test','App\Http\Controllers\Api\Yuda\SystemController@test');//加密测试
        $api->get('/systemsync/posttest','App\Http\Controllers\Api\Yuda\SystemController@posttest');//模拟post数据请求裕达接口
        $api->post('/systemsync/receiveTest','App\Http\Controllers\Api\Yuda\SystemController@distribute');//内部模仿裕达接口测试

    });

});

//protected with JWT
$api->version('v1', ['middleware' => 'api.auth', 'providers' => 'jwt'], function ($api) {

    $api->group(['prefix' => '/api'], function($api){
        $api->get('/dashboard/{key}', 'App\Http\Controllers\Api\DashboardController@getInitData');

        $api->get('/user', 'App\Http\Controllers\Api\UserController@getUsers');
        $api->get('/user/{id}', 'App\Http\Controllers\Api\UserController@getUser');
        $api->post('/user', 'App\Http\Controllers\Api\UserController@addUser');
        $api->put('/user', 'App\Http\Controllers\Api\UserController@updateUser');
        $api->delete('/user/{id}', 'App\Http\Controllers\Api\UserController@deleteUser');

        $api->get('/cardSummaries', 'App\Http\Controllers\Api\CardController@getCardSummaries');
        $api->get('/cardNewSummaries', 'App\Http\Controllers\Api\CardController@getNewCardSummaries');
        $api->get('/cardDetail/{id}', 'App\Http\Controllers\Api\CardController@getCardDetail');
        $api->get('/card/{id}', 'App\Http\Controllers\Api\CardController@getCard');
        $api->get('/cardSearch/{val}', 'App\Http\Controllers\Api\CardController@searchCardList');
        $api->get('/cardTicketList/{id}', 'App\Http\Controllers\Api\TicketController@getCardTicketList');
        $api->get('/cardTotal', 'App\Http\Controllers\Api\CardController@getTotal');

        $api->get('/ticket_template', 'App\Http\Controllers\Api\TicketTemplateController@getTicketTemplateList');
        $api->get('/ticket_template/{id}', 'App\Http\Controllers\Api\TicketTemplateController@getTicketTemplate');
        $api->post('/ticket_template', 'App\Http\Controllers\Api\TicketTemplateController@addTicketTemplate');
        $api->put('/ticket_template', 'App\Http\Controllers\Api\TicketTemplateController@updateTiecktTemplate');
        $api->delete('/ticket_template/{id}', 'App\Http\Controllers\Api\TicketTemplateController@deleteTicketTemplate');
        $api->get('/ticket_template_type_list', 'App\Http\Controllers\Api\TicketTemplateController@getTicketTemplateTypeList');
        $api->get('/ticket_template_usable_list', 'App\Http\Controllers\Api\TicketTemplateController@getTicketTemplateUsableList');

        $api->get('/mass', 'App\Http\Controllers\Api\MassController@getDefaultMass');
        $api->post('/mass', 'App\Http\Controllers\Api\MassController@getQueryResult');
        $api->get('/mass/template', 'App\Http\Controllers\Api\MassController@getMassTemplateList');
        $api->get('/mass/template/{id}', 'App\Http\Controllers\Api\MassController@getMassTemplate');
        $api->post('/mass/template', 'App\Http\Controllers\Api\MassController@saveMassTemplate');
        $api->delete('/mass/template/{id}', 'App\Http\Controllers\Api\MassController@delMassTemplate');
        $api->get('/mass/history', 'App\Http\Controllers\Api\MassController@getMassHistory');
        $api->post('/mass/send', 'App\Http\Controllers\Api\MassController@send');
        $api->get('/mass/sendtop', 'App\Http\Controllers\Api\MassController@getSendTop');

        $api->get('/propertytemplate', 'App\Http\Controllers\Api\PropertyTemplateController@getPropertyTemplateList');
        $api->get('/propertytemplate/{id}', 'App\Http\Controllers\Api\PropertyTemplateController@getPropertyTemplate');
        $api->post('/propertytemplate', 'App\Http\Controllers\Api\PropertyTemplateController@addPropertyTemplate');
        $api->put('/propertytemplate', 'App\Http\Controllers\Api\PropertyTemplateController@updatePropertyTemplate');
        $api->delete('/propertytemplate/{id}', 'App\Http\Controllers\Api\PropertyTemplateController@deletePropertyTemplate');
        $api->get('/icons', 'App\Http\Controllers\Api\PropertyTemplateController@getIcons');

        $api->get('/event', 'App\Http\Controllers\Api\EventRuleController@getEventList');
        $api->post('/event', 'App\Http\Controllers\Api\EventRuleController@addEventRule');
        $api->put('/event', 'App\Http\Controllers\Api\EventRuleController@updateEventRule');
        $api->delete('/event/{id}', 'App\Http\Controllers\Api\EventRuleController@delEventRule');
        $api->get('/event/rules', 'App\Http\Controllers\Api\EventRuleController@getEventRules');
        $api->get('/event/jobs', 'App\Http\Controllers\Api\EventRuleController@getJobList');
        $api->get('/eventRulelist', 'App\Http\Controllers\Api\EventRuleController@getEventRuleList');

        $api->get('/commodity', 'App\Http\Controllers\Api\CommodityController@getCommodityList');
        $api->get('/commodityCategory/{id}', 'App\Http\Controllers\Api\CommodityController@getCommodityListWithCategory');
        $api->get('/commoditywithoutsuit', 'App\Http\Controllers\Api\CommodityController@getCommoditySpecificationsWithoutSuit');
        $api->get('/commodity/{id}', 'App\Http\Controllers\Api\CommodityController@getCommodity');
        $api->get('/commodityMarketer/{id}', 'App\Http\controllers\Api\CommodityController@getCommodityMarketer');
        $api->post('/commodity', 'App\Http\Controllers\Api\CommodityController@addCommodity');
        $api->put('/commodity', 'App\Http\Controllers\Api\CommodityController@updateCommodity');
        $api->delete('/commodity/{id}', 'App\Http\Controllers\Api\CommodityController@deleteCommodity');

        $api->get('/commodity_category', 'App\Http\Controllers\Api\CommodityController@getCategoryList');
        $api->post('/commodity_category', 'App\Http\Controllers\Api\CommodityController@addCategory');
        $api->put('/commodity_category', 'App\Http\Controllers\Api\CommodityController@editCategory');
        $api->delete('/commodity_category/{id}', 'App\Http\Controllers\Api\CommodityController@deleteCategory');
        $api->get('/commodity_usable_category', 'App\Http\Controllers\Api\CommodityController@getUsableCategoryList');
        $api->get('/getCommoditiesByConditions/{conditions}', 'App\Http\Controllers\Api\CommodityController@getCommoditiesByConditions');

        $api->get('/commodity_category', 'App\Http\Controllers\Api\CommodityController@getCommodityCategoryList');
        $api->get('/commodity_category/{id}', 'App\Http\Controllers\Api\CommodityController@getCommodityCategory');
        $api->post('/commodity_category', 'App\Http\Controllers\Api\CommodityController@addCommodityCategory');
        $api->put('/commodity_category', 'App\Http\Controllers\Api\CommodityController@editCommodityCategory');
        $api->delete('/commodity_category/{id}', 'App\Http\Controllers\Api\CommodityController@deleteCommodityCategory');
        $api->get('/getCommoditiessByConditions/{conditions}', 'App\Http\Controllers\Api\CommodityController@getCommoditiesByConditions');

        $api->post('/image', 'App\Http\Controllers\Api\CommodityController@getImage');
        $api->put('/image', 'App\Http\Controllers\Api\CommodityController@delImage');
        $api->post('/getCommoditiesListByPage', 'App\Http\Controllers\Api\CommodityController@getCommoditiesListByPage');
        $api->post('/getCommoditiesByArray','App\Http\Controllers\Api\CommodityController@getCommoditiesByArray');

        $api->get('/redeemcode', 'App\Http\Controllers\Api\RedeemCodeController@getRedeemCodeList');
        $api->get('/redeemcode/{id}', 'App\Http\Controllers\Api\RedeemCodeController@getRedeemCode');
        $api->get('/generationCode/{id}/{amount}', 'App\Http\Controllers\Api\RedeemCodeController@generationCode');
        $api->post('/redeemcode', 'App\Http\Controllers\Api\RedeemCodeController@addRedeemCode');
        $api->put('/redeemcode', 'App\Http\Controllers\Api\RedeemCodeController@updateRedeemCode');
        $api->delete('/redeemcode/{id}', 'App\Http\Controllers\Api\RedeemCodeController@deleteRedeemCode');
        $api->get('/redeemcodeHistoryList', 'App\Http\Controllers\Api\RedeemCodeController@RedeemCodeHistoryList');
        $api->get('/redeemcodeHistory/{date}/{type}', 'App\Http\Controllers\Api\RedeemCodeController@RedeemCodeHistory');

        $api->post('/write_off', 'App\Http\Controllers\Api\AssetController@write_off');
        $api->post('/createGoodsOrder', 'App\Http\Controllers\Api\OrderController@createGoodsOrder');
        $api->post('/createBalanceOrder', 'App\Http\Controllers\Api\OrderController@createBalanceOrder');
        $api->post('/createConsumeOrder', 'App\Http\Controllers\Api\OrderController@createConsumeOrder');
        $api->post('/initCardData', 'App\Http\Controllers\Api\CardController@initCardData');
        $api->post('/editPassword', 'App\Http\Controllers\Api\AuthController@editPassword');
        $api->get('/statistical', 'App\Http\Controllers\Api\OperatingRecordController@getstatistical');

        $api->get('/order/{id}', 'App\Http\Controllers\Api\OrderController@getOrder');
        $api->get('/order_data/{option}', 'App\Http\Controllers\Api\OrderController@getOrderData');
        $api->get('/saleTop', 'App\Http\Controllers\Api\OrderController@getSaleTop');
        $api->post('/deliverList', 'App\Http\Controllers\Api\OrderController@getDeliverList');
        $api->post('/deliver', 'App\Http\Controllers\Api\OrderController@deliver');
        $api->post('/editAddress', 'App\Http\Controllers\Api\OrderController@editAddress');
        $api->post('/refundTotal', 'App\Http\Controllers\Api\RefundController@getRefundTotal');
        $api->post('/refunds', 'App\Http\Controllers\Api\RefundController@getRefundsData');
        $api->get('/refund/{id}', 'App\Http\Controllers\Api\RefundController@getRefundData');
        $api->post('/dealRefund', 'App\Http\Controllers\Api\RefundController@dealRefund');

        $api->get('/shopConfig','App\Http\Controllers\Api\ShopConfigController@index');
        $api->post('/saveShopTitle','App\Http\Controllers\Api\ShopConfigController@saveShopTitle');
        $api->post('/saveShopCategory','App\Http\Controllers\Api\ShopConfigController@saveShopCategory');
        $api->post('/saveShopPage','App\Http\Controllers\Api\ShopConfigController@saveShopPage');
        $api->get('/deleteShop/{id}','App\Http\Controllers\Api\ShopConfigController@deleteShop');
        $api->get('/deleteCategory/{shopId}/{categoryId}','App\Http\Controllers\Api\ShopConfigController@deleteCategory');
        $api->get('/getShopPage','App\Http\Controllers\Api\ShopConfigController@getShopPage');

        $api->get('/statistical', 'App\Http\Controllers\Api\OperatingRecordController@getstatistical');

        $api->put('/setting', 'App\Http\Controllers\Api\SettingController@setSetting');
        $api->get('/settingTheme', 'App\Http\Controllers\Api\SettingController@getTheme');

        $api->get('/statistics', 'App\Http\Controllers\Api\StatisticsController@getDateList');
        $api->get('/statisticsDaysData', 'App\Http\Controllers\Api\StatisticsController@getDaysData');
        $api->post('/statistics', 'App\Http\Controllers\Api\StatisticsController@getStatisticsDate');
        $api->post('/commoditiesStatistics', 'App\Http\Controllers\Api\StatisticsController@getCommoditiesStatistics');
        $api->post('/commodityStatistics/{id}', 'App\Http\Controllers\Api\StatisticsController@getCommodityStatisticsData');

        $api->get('/genes/{name}/{pageindex}/{pagesize}', 'App\Http\Controllers\Api\Health\GeneController@getGenes');
        $api->post('/create_gene', 'App\Http\Controllers\Api\Health\GeneController@createGene');
        $api->post('/update_gene', 'App\Http\Controllers\Api\Health\GeneController@updateGene');
        $api->post('/save_site', 'App\Http\Controllers\Api\Health\GeneController@saveSite');

        $api->post('/upload', 'App\Http\Controllers\Api\Health\ConfigController@getFile');

        $api->post('/siteData', 'App\Http\Controllers\Api\Health\SiteDataController@getSiteData');
        $api->put('/siteData', 'App\Http\Controllers\Api\Health\SiteDataController@editSiteData');

        $api->get('/experimentData/{searchDTO}', 'App\Http\Controllers\Api\Health\ExperimentDataController@search');
        $api->get('/experimentData/getDetailById/{experiment_data_id}', 'App\Http\Controllers\Api\Health\ExperimentDataController@getDetailById');
        $api->get('/experimentData/getRiskById/{experiment_data_id}', 'App\Http\Controllers\Api\Health\ExperimentDataController@getRiskById');
        $api->post('/projectData/save', 'App\Http\Controllers\Api\Health\ProjectDataController@saveProjectData');
        $api->post('/riskData/save', 'App\Http\Controllers\Api\Health\ProjectDataController@saveRiskData');
        $api->post('/projectData/search', 'App\Http\Controllers\Api\Health\ProjectDataController@searchProjectData');

        $api->get('/experiments', 'App\Http\Controllers\Api\Health\ExperimentController@get_experiments');
        $api->get('/experiment/{id}', 'App\Http\Controllers\Api\Health\ExperimentController@get_experiment');
        $api->get('/experiments/get_by_projectId', 'App\Http\Controllers\Api\Health\ExperimentController@get_by_projectId');

        $api->post('/reservation', 'App\Http\Controllers\Api\Health\ReservationController@getReservations');


        $api->get('/get_sites_by_projectId/{projectId}', 'App\Http\Controllers\Api\Health\ExperimentController@get_sites_by_projectId');
        $api->post('/save_experiment', 'App\Http\Controllers\Api\Health\ExperimentController@save_experiment');
        $api->post('/save_project', 'App\Http\Controllers\Api\Health\ExperimentController@save_project');
        $api->post('/save_project_site', 'App\Http\Controllers\Api\Health\ExperimentController@save_site');
        $api->get('/get_risk_by_projectId/{projectId}', 'App\Http\Controllers\Api\Health\ExperimentController@get_risk_by_projectId');
        $api->post('/save_risk', 'App\Http\Controllers\Api\Health\ExperimentController@save_risk');
         $api->post('/save_circumRisk', 'App\Http\Controllers\Api\Health\ExperimentController@save_circumRisk');
         
        $api->get('/searchAnswerByCode/{code}','App\Http\Controllers\Api\Health\QuestionnaireController@searchAnswerByCode');
        $api->get('/initQuestionnaire','App\Http\Controllers\Api\Health\QuestionnaireController@initQuestionnaire');
        $api->get('/getQuestionnaireByPage/{page}','App\Http\Controllers\Api\Health\QuestionnaireController@getQuestionnaireByPage');
        $api->post('/addAnswer','App\Http\Controllers\Api\Health\QuestionnaireController@addAnswer');

        $api->post('/addCustomer','App\Http\Controllers\Api\Health\CustomerController@addCustomer');
        $api->post('/editCustomer','App\Http\Controllers\Api\Health\CustomerController@editCustomer');
//        $api->get('/getCustomerTotal/{name}','App\Http\Controllers\Api\Health\CustomerController@getCustomerTotal');
        $api->get('/getCustomerList/{page}/{name}','App\Http\Controllers\Api\Health\CustomerController@getCustomerList');
        $api->get('/customer/init','App\Http\Controllers\Api\Health\CustomerController@init');

        $api->get('/getProgresses','App\Http\Controllers\Api\Health\BarcodeController@getProgressConfig');
        $api->post('/addBarcodes','App\Http\Controllers\Api\Health\BarcodeController@addBarcodes');
        $api->get('/getBarcodeInfo/{code}','App\Http\Controllers\Api\Health\BarcodeController@getBarcodeInfo');
        $api->get('/addBarcode/{code}','App\Http\Controllers\Api\Health\BarcodeController@addBarcode');
        $api->get('/getBarcodes/{page}/{pagesize}/{code}','App\Http\Controllers\Api\Health\BarcodeController@getBarcodes');
        $api->get('/changeBarcodeInfo/{barcodeId}/{experimentId}/{progressId}','App\Http\Controllers\Api\Health\BarcodeController@changeBarcodeInfo');


        $api->get('/loginRecords/{option}/{page}/{search?}','App\Http\Controllers\Api\LoginRecordController@getLoginRecords');
        $api->get('/operatingRecords/{page}/{search}','App\Http\Controllers\Api\OperatingRecordController@getOperatingRecords');

        $api->get('/backup', 'App\Http\Controllers\Api\BackupController@backup');
        $api->get('/backupHistory', 'App\Http\Controllers\Api\BackupController@getLastBackTime');

        $api->post('/syncFailRecords', 'App\Http\Controllers\Api\Yuda\OrderSyncController@getSyncFailRecord');//查询订单同步失败记录
        $api->get('/syncSuccess/{id}', 'App\Http\Controllers\Api\Yuda\OrderSyncController@syncSuccess');//将一个记录置为同步成功
        $api->get('/againSync/{id}', 'App\Http\Controllers\Api\Yuda\OrderSyncController@againSync');//重试同步
    });

});

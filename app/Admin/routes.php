<?php

use Illuminate\Routing\Router;

Admin::routes();

Admin::registerAuthRoutes();

Route::resource('admin/auth/users', \App\Admin\Controllers\CustomUserController::class)->middleware(config('admin.route.middleware'));

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/auth/login', function () {
        return view("admin.login");
    });
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('/auth/news', NewsController::class);
    $router->resource('/auth/copos', CoposController::class);
    $router->resource('/auth/author', AuthorController::class);
    $router->resource('/auth/tags', TagController::class);
    $router->resource('/auth/newsposition', NewsPositionController::class);
    $router->resource('/auth/editorhasredactors', EditorHasRedactorsController::class);
    $router->resource('/auth/feed', FeedController::class);
    $router->resource('/auth/medios', MediosController::class);
    $router->resource('/auth/feedsocials', FeedSocialsController::class);
    $router->resource('/auth/weather', WeatherController::class);
    $router->resource('/auth/embedfacebook', EmbedFacebookController::class);
    $router->resource('/auth/cars', CarsController::class);
    $router->resource('/auth/houses', HousesController::class);
    $router->resource('/auth/descriptionpostalcodes', DescriptionPostalCodesController::class);
    $router->resource('/auth/announcementevent', AnnouncementEventController::class);
    $router->resource('/auth/copomanagers', CopoManagersController::class);
    $router->resource('/auth/copomessages', CopoMessagesController::class);
    $router->resource('/auth/newscategories', NewsCategories::class);
    $router->resource('/auth/datacopo', DataCopoController::class);

    // $router->resource('/auth/embeds', EmbedsController::class);


    $router->get('/auth/embeds-clasificados', 'EmbedsController@GetInfoEmbedsClasificados')->name('admin.embeds');
    $router->get('/auth/embeds-empleos', 'EmbedsController@GetInfoEmbedsEmpleos')->name('admin.embedsempleos');

    $router->get('/api/slideautonews', 'NewsController@slideAutoNews')->name('admin.slideautonews');
    $router->get('/api/swipeleftnews', 'NewsController@swipeLeftNews')->name('admin.swipeleftnews');
    $router->get('/api/swiperightnews', 'NewsController@swipeRightNews')->name('admin.swiperightnews');
    $router->get('/api/findnewstoslide', 'NewsController@findNewsToSlide')->name('admin.findnewstoslide');
    $router->get('/api/exportnews', 'NewsController@exportNews')->name('admin.exportnews');
    $router->get('/api/findnews', 'NewsController@findNews')->name('admin.findnews');
    $router->get('/api/postalcodes', 'PostalCodeController@findPostalCodes')->name('admin.findpostalcodes');
    $router->get('/api/postalcodescopos', 'PostalCodeController@findPostalCodesToCopos')->name('admin.findpostalcodescopos');
    $router->get('/api/findmunicipality', 'PostalCodeController@findMunicipality')->name('admin.findmunicipality');
    $router->get('/api/findstate', 'PostalCodeController@findState')->name('admin.findstate');
    $router->get('/api/findeditors', 'EditorHasRedactorsController@findEditors')->name('admin.findeditors');
    $router->get('/api/findredactors', 'EditorHasRedactorsController@findRedactors')->name('admin.findredactors');





});

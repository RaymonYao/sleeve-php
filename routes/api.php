<?php
/** @var Router $router */

use FastRoute\Route;
use Laravel\Lumen\Routing\Router;

$router->get('/theme/by/names', 'ThemeController@list');
$router->get('/banner/name/{name}', 'BannerController@getBanner');
$router->get('category/grid/all', 'CategoryController@getCategory');
$router->get('/activity/name/{name}', 'ActivityController@getActivity');
$router->get('/theme/name/{name}/with_spu', 'SpuController@getSpuListByTheme');
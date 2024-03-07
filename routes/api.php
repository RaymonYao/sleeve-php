<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/api/login/{code}', 'LoginController@login');
$router->get('/api/test', 'LoginController@test');
$router->group(['middleware' => 'jwt', 'prefix' => '/api/sys'], function () use ($router) {
    $router->post('/dept', 'SysController@deptList');
    $router->post('/user', 'SysController@userList');
    $router->post('/userDept', 'SysController@userDept');
    $router->post('/upload', 'SysController@upload');
});

$router->group(['middleware' => 'jwt', 'prefix' => '/api/ticket'], function () use ($router) {
    $router->post('/', 'TicketController@list');
    $router->post('/detail', 'TicketController@detail');
    $router->post('/save', 'TicketController@save');
    $router->post('/cancel', 'TicketController@cancel');
    $router->post('/finish', 'TicketController@finish');
    $router->post('/assign', 'TicketController@assign');
    $router->post('/complete', 'TicketController@complete');
    $router->post('/urge', 'TicketController@urge');
    $router->post('/getOpLogList', 'TicketController@getOpLogList');
});

$router->group(['middleware' => 'jwt', 'prefix' => '/api/post'], function () use ($router) {
    $router->post('/', 'PostController@list');
    $router->post('/reply', 'PostController@reply');
});

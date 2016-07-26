<?php

use csv\Controller\UserController;
use csv\Model\Model;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;

const CSV_FILE_PATH = 'file.csv';
$app->register(new ServiceControllerServiceProvider());

$app['users.controller'] = function ($app) {
    return new UserController($app['users.model']);
};
$app['users.model'] = function (){
    return new Model(CSV_FILE_PATH);
};

$users = $app['controllers_factory'];

$users->get('/', 'users.controller:get');
$users->post('/', 'users.controller:post');
$users->get('/{offset}', 'users.controller:getRow');
$users->put('/{offset}', 'users.controller:put');
$users->delete('/{offset}', 'users.controller:delete');

$app->mount('/', $users);
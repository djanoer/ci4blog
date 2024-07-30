<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes) {

  $routes->group('', ['filter' => 'cifilter:auth'], static function ($routes) {
    // $routes->view('example-page','example-page');
    $routes->get('home', 'AdminController::index', ['as' => 'admin.home']);
    $routes->get('logout', 'AdminController::logoutHandler', ['as' => 'admin.logout']);
    $routes->get('profile', 'AdminController::profile', ['as' => 'admin.profile']);
    $routes->get('categories', 'AdminController::categories', ['as' => 'categories']);
    $routes->post('add-category', 'AdminController::addCategory', ['as' => 'add-category']);
    $routes->get('get-categories','AdminController::getCategories',['as'=>'get-categories']);
    $routes->get('get-category', 'AdminController::getCategory',['as'=>'get-category']);
    $routes->post('update-category', 'AdminController::updateCategory',['as'=>'update-category']);
    $routes->get('delete-category', 'AdminController::deleteCategory',['as'=>'delete-category']);
    $routes->get('reorder-categories', 'AdminController::reorderCategories',['as'=>'reorder-categories']);
    $routes->get('get-parent-categories', 'AdminController::getParentCategories',['as'=>'get-parent-categories']);
    $routes->post('add-subcategory', 'AdminController::addSubCategory',['as'=>'add-subcategory']);
    $routes->get('get-subcategories', 'AdminController::getSubCategories',['as'=>'get-subcategories']);

  });

  $routes->group('', ['filter' => 'cifilter:guest'], static function ($routes) {
    // $routes->view('example-auth','example-auth');
    $routes->get('login', 'AuthController::loginForm', ['as' => 'admin.login.form']);
    $routes->post('login', 'AuthController::loginHandler', ['as' => 'admin.login.handler']);
    $routes->get('forgot-password', 'AuthController::forgotForm', ['as' => 'admin.forgot.form']);
    $routes->post('send-password-reset-link', 'AuthController::sendPasswordResetLink', ['as' => 'send_password_reset_link']);
    $routes->get('password/reset/(:any)', 'AuthController::resetPassword/$1', ['as' => 'admin.reset-password']);
    $routes->post('reset-password-handler/(:any)', 'AuthController::resetPasswordHandler/$1', ['as' => 'reset-password-handler']);
  });
});

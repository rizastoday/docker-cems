<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/', 'Auth::index');
$routes->post('/auth_http', 'Auth::auth_http');
$routes->get('/logout', 'Auth::logout');

$routes->group('dashboard', ['filter' => 'auth'], function($routes){
	$routes->get('index', 'Dashboard::index');
	$routes->get('trending', 'Dashboard::trending');
	$routes->get('reporting', 'Dashboard::reporting');
	$routes->get('deviation', 'Dashboard::deviation');
	$routes->get('sync', 'Dashboard::sync');
	$routes->get('showcase', 'Dashboard::showcase');
	$routes->post('ajax_list', 'Dashboard::ajax_list');
});

$routes->group('setting', ['filter' => 'auth'], function($routes){
	$routes->get('companyProfile', 'Setting::companyProfile');
	$routes->get('sispek', 'Setting::sispek');
	$routes->get('notification', 'Setting::notification');
	$routes->get('parameter', 'Setting::parameter');
	$routes->get('email-list', 'Setting::email');
	$routes->get('account-management', 'Setting::accountManagement');
	$routes->get('schedule', 'Setting::schedule');
	$routes->get('get-user', 'Setting::getUser');
	$routes->get('get-group', 'Setting::getGroup');
	$routes->post('saveCompanyProfile/(:any)', 'Setting::saveCompanyProfile/$1');
	$routes->post('saveSispek/(:any)', 'Setting::saveSispek/$1');
	$routes->post('saveNotification/(:any)', 'Setting::saveNotification/$1');
	$routes->post('ajax_list', 'Setting::ajax_list');
	$routes->post('executeScheduleMaintenance', 'Setting::executeScheduleMaintenance');
	$routes->post('add-user', 'Setting::addUser');
	$routes->post('delete-user', 'Setting::deleteUser');
	$routes->post('change-password', 'Setting::changePassword');
});

$routes->group('cems', ['filter' => 'auth'], function($routes){
	$routes->get('index', 'Cems::index');
	$routes->get('details/(:any)', 'Cems::details/$1');
	$routes->get('add', 'Cems::add');
	$routes->post('saveOnlyCems', 'Cems::saveOnlyCems');
	$routes->post('deleteCems', 'Cems::deleteCems');
	$routes->post('saveOnlyParameter', 'Cems::saveOnlyParameter');
	$routes->post('changePassword', 'Cems::changePassword');
});

$routes->group('history', ['filter' => 'auth'], function($routes){
	$routes->post('index', 'History::index');
	$routes->post('ajax_list', 'History::ajax_list');
});

$routes->group('api', function($routes){
	$routes->group('resource', function($routes){
		$routes->get('getcems', 'API/Resource::GETCEMS');
		$routes->get('getparameter', 'API/Resource::GETPARAMETER');
	});
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

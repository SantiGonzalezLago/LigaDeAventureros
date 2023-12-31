<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(true);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/privacy', 'Home::privacy');

$routes->post('login', 'Login::login');
$routes->post('login/onetap', 'Login::onetap');
$routes->get('login/google', 'Login::google');
$routes->get('logout', 'Login::logout');

$routes->get('settings','Profile::settings');
$routes->post('settings','Profile::settings_post');
$routes->post('settings/delete-account','Profile::delete_account');
$routes->get('profile/(:segment)','Profile::index/$1');

$routes->get('all-characters', 'Profile::all_characters');
$routes->get('all-characters/(:segment)', 'Profile::all_characters/$1');

$routes->post('new-character', 'Profile::new_character');
$routes->post('update-character', 'Profile::update_character');

$routes->get('master', 'Master::index');
$routes->post('master/kick', 'Master::kick');
$routes->get('master/sheets', 'Master::sheets');
$routes->post('master/validate-sheet', 'Master::validate_sheet');
$routes->post('master/reject-sheet', 'Master::reject_sheet');
$routes->get('master/adventures', 'Master::adventures');
$routes->post('master/adventures', 'Master::adventures_post');
$routes->get('master/adventure/(:segment)', 'Master::adventure/$1');
$routes->get('master/edit-adventure/(:segment)', 'Master::edit_adventure/$1');
$routes->post('master/edit-adventure/(:segment)', 'Master::edit_adventure_post/$1');
$routes->get('master/new-session', 'Master::new_session');
$routes->post('master/new-session', 'Master::new_session_post');
$routes->get('master/publish', 'Master::publish');
$routes->post('master/publish', 'Master::publish_post');
$routes->post('master/delete-session', 'Master::delete_session');
$routes->get('master/edit-session/(:segment)', 'Master::edit_session/$1');
$routes->post('master/edit-session/(:segment)', 'Master::edit_session_post/$1');

$routes->get('admin', 'Admin::index');
$routes->get('admin/logs', 'Admin::logs');
$routes->get('admin/logs/(:segment)', 'Admin::logs/$1');
$routes->post('admin/logs', 'Admin::logs_post');
$routes->get('admin/settings', 'Admin::settings');
$routes->post('admin/settings', 'Admin::settings_post');
$routes->get('admin/users', 'Admin::users');
$routes->get('admin/users/(:segment)', 'Admin::users/$1');
$routes->post('admin/user-toggle-master', 'Admin::user_toggle_master');
$routes->post('admin/user-toggle-admin', 'Admin::user_toggle_admin');
$routes->post('admin/user-ban', 'Admin::user_ban');
$routes->post('admin/user-unban', 'Admin::user_unban');

$routes->post('adventure/data-ajax', 'Adventure::data_ajax');

$routes->get('calendar', 'Session::calendar');
$routes->get('calendar/(:segment)/(:segment)', 'Session::calendar/$1/$2');
$routes->get('session/view/(:segment)', 'Session::view/$1');

$routes->post('session/join', 'Session::join');
$routes->post('session/swap', 'Session::swap');
$routes->post('session/cancel', 'Session::cancel');

$routes->get('cron/delete-sheets', 'Cron::delete_sheets');
$routes->get('cron/delete-adventure-thumbnails', 'Cron::delete_adventure_thumbnails');
$routes->get('cron/delete-accounts-requested', 'Cron::delete_accounts_requested');
$routes->get('cron/delete-accounts-inactive', 'Cron::delete_accounts_inactive');

/*
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
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

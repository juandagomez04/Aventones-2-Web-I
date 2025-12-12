<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');

// Auth
$routes->match(['get', 'post'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Passwordless
$routes->post('passwordless/send-link', 'PasswordlessController::sendLink');
$routes->get('passwordless/login/(:any)', 'PasswordlessController::login/$1');

// Register
$routes->match(['get', 'post'], 'register/passenger', 'AuthController::registerPassenger');
$routes->match(['get', 'post'], 'register/driver', 'AuthController::registerDriver');
$routes->get('activate/(:any)', 'AuthController::activate/$1');

// Admin
$routes->get('admin', 'AdminController::index');
$routes->post('admin/users/action', 'AdminController::action');
$routes->match(['get','post'], 'admin/reports/searches', 'AdminController::searchReport');


// Rides
$routes->get('rides/search', 'RidesController::search');
$routes->get('rides/details/(:num)', 'RidesController::details/$1');

$routes->get('rides/my', 'RidesController::my');
$routes->post('rides/action', 'RidesController::action');

$routes->get('rides/new', 'RidesController::new');
$routes->post('rides/store', 'RidesController::store');

$routes->get('rides/edit/(:num)', 'RidesController::edit/$1');
$routes->post('rides/update/(:num)', 'RidesController::update/$1');

// Vehicles (driver)
$routes->get('vehicles', 'VehiclesController::index');
$routes->get('vehicles/edit/(:num)', 'VehiclesController::edit/$1');
$routes->post('vehicles/store', 'VehiclesController::store');
$routes->post('vehicles/update/(:num)', 'VehiclesController::update/$1');
$routes->get('vehicles/confirm-delete/(:num)', 'VehiclesController::confirmDelete/$1');
$routes->post('vehicles/delete/(:num)', 'VehiclesController::delete/$1');

// Bookings
$routes->get('bookings', 'BookingsController::index');
$routes->post('bookings/action', 'BookingsController::action');

// Profile
$routes->get('profile', 'ProfileController::index');
$routes->get('profile/edit', 'ProfileController::edit');
$routes->post('profile/update', 'ProfileController::update');
$routes->post('profile/update-password', 'ProfileController::updatePassword');
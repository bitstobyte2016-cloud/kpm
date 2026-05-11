<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('/cart', 'HomeController::cart');
$routes->get('/categories', 'HomeController::categories');
$routes->get('/register', 'RegistCont::index');
$routes->get('/search', 'HeaderCont::search');

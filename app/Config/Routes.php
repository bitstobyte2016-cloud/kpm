<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('/cart', 'HomeController::cart');
$routes->get('/categories', 'HomeController::categories');
$routes->get('/categories_all', 'HomeController::categories_all');
$routes->get('/bands_all', 'HomeController::bands_all');
$routes->get('/preorder', 'HomeController::preorder');
$routes->get('/onhand', 'HomeController::onhand');
$routes->get('/register', 'RegistCont::index');
$routes->get('/search', 'HeaderCont::search');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//home routes
$routes->get('/', 'HomeController::index');
$routes->get('/cart', 'HomeController::cart');
$routes->get('/categories', 'HomeController::categories');
$routes->get('/categories_all', 'HomeController::categories_all');
$routes->get('/bands_all', 'HomeController::bands_all');
$routes->get('/preorder', 'HomeController::preorder');
$routes->get('/onhand', 'HomeController::onhand');

// Footer routes
$routes->get('/faq', 'FooterCont::faq');
$routes->get('/shipping-info', 'FooterCont::shipping_info');
$routes->get('/returns', 'FooterCont::returns');
$routes->get('/contact', 'FooterCont::contact');
$routes->get('/terms', 'FooterCont::terms');
$routes->get('/privacy', 'FooterCont::privacy');

//register routes
$routes->get('/register', 'RegistCont::index');

//header routes
$routes->get('/search', 'HeaderCont::search');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//home routes
$routes->get('/', 'HomeController::index');
$routes->get('/categories', 'HomeController::categories');

// Footer routes
$routes->get('/faq', 'FooterCont::faq');
$routes->get('/shipping-info', 'FooterCont::shipping_info');
$routes->get('/returns', 'FooterCont::returns');
$routes->get('/contact', 'FooterCont::contact');
$routes->get('/terms', 'FooterCont::terms');
$routes->get('/privacy', 'FooterCont::privacy');


//header routes
$routes->get('/search', 'HeaderCont::search');
$routes->get('/register', 'HeaderCont::register');
$routes->get('/cart', 'HeaderController::cart');
$routes->get('/categories_all', 'HeaderController::categories_all');
$routes->get('/bands_all', 'HeaderController::bands_all');
$routes->get('/preorder', 'HeaderController::preorder');
$routes->get('/onhand', 'HeaderController::onhand');

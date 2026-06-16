<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//home routes
$routes->get('/', 'HomeController::index');

// Footer routes
$routes->get('/faq', 'FooterCont::faq');
$routes->get('/shipping_info', 'FooterCont::shipping_info');
$routes->get('/returns', 'FooterCont::returns');
$routes->get('/contact', 'FooterCont::contact');
$routes->get('/terms', 'FooterCont::terms');
$routes->get('/privacy', 'FooterCont::privacy');


//header routes
$routes->get('/search', 'HeaderCont::search');
$routes->post('live-search', 'HeaderCont::liveSearch');
$routes->get('/signin', 'HeaderCont::signin');
$routes->get('/cart', 'HeaderCont::cart');

$routes->get('/categories/(:num)','HeaderCont::categoriesAll/$1');
$routes->get('/bands_all', 'HeaderCont::bands_all');
$routes->get('/preorder', 'HeaderCont::preorder');
$routes->get('/onhand', 'HeaderCont::onhand');



//home controller routes
$routes->post('getProductsByCat','HomeController::getProductsByCat');
$routes->get('register','HomeController::register');
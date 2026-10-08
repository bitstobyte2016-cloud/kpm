<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//home routes
$routes->get('/', 'HomeController::index');
$routes->get('/test','HeaderCont::test');

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
$routes->post('login', 'HeaderCont::login');
$routes->get('register','HeaderController::register');
$routes->get('getIndiaData', 'HeaderCont::getIndiaData');
$routes->post('getIndiaData', 'HeaderCont::getIndiaData');
$routes->post( '/verify', 'HeaderCont::verify');
$routes->post('/verifyOtp','HeaderCont::verifyOtp');
$routes->post('resendE','HeaderCont::resendEmailOtp');
$routes->post('registerUser','HeaderCont::registerUser');
$routes->get('/cart', 'HeaderCont::cart');
$routes->get('/categories/(:num)','HeaderCont::categoriesAll/$1');
$routes->get('/bands_all', 'HeaderCont::bands_all');
$routes->get('/preorder', 'HeaderCont::preorder');
$routes->get('/onhand', 'HeaderCont::onhand');



//home controller routes
$routes->post('getProductsByCat','HomeController::getProductsByCat');
$routes->post('getProductsByCategory','HomeController::getProductsByCategory');
$routes->post('getProductsByBand','HomeController::getProductsByBand');
$routes->post('forgotPassword', 'HomeController::forgotPassword');

//home product routes
$routes->get('product/(:segment)', 'HomeController::getProductDetails/$1');
$routes->post('product/save-review', 'HomeController::saveProductReview');


//user account routes
$routes->get('userhome', 'UserController::accountHome');
$routes->post('accountHomeContent', 'UserController::accountHomeContent');
$routes->post('editProfile', 'UserController::editProfile');
$routes->post('updateProfile', 'UserController::updateProfile');
$routes->post('changePassword', 'UserController::changePassword');
$routes->post('redeemReward', 'UserController::redeemReward');
$routes->post('logout', 'UserController::logout');

$routes->post('accountAddresses', 'UserController::accountAddresses');
$routes->post('setDefaultAddress', 'UserController::setDefaultAddress');
$routes->post('accountAddAddress', 'UserController::accountAddAddress');
$routes->post('saveAccountAddress', 'UserController::saveAccountAddress');
$routes->post('deleteAccountAddress', 'UserController::deleteAccountAddress');
$routes->post('editAccountAddress', 'UserController::editAccountAddress');
$routes->post('updateAccountAddress', 'UserController::updateAccountAddress');

$routes->post('accountOrders', 'UserController::accountOrders');
$routes->post('orderDetails', 'UserController::orderDetails');
$routes->post('getInvoiceID', 'UserController::getInvoiceID');
$routes->post('generateInvoice', 'UserController::generateInvoice');
$routes->post('sendOrderMessage', 'UserController::sendOrderMessage');
$routes->post('getOrderMessages', 'UserController::getOrderMessages');
$routes->post('cancelOrder', 'UserController::cancelOrder');
$routes->post('emailCancelOrder', 'UserController::emailCancelOrder');

$routes->post('accountWishlists', 'UserController::accountWishlists');
$routes->post('accountWishlistDetail', 'UserController::accountWishlistDetail');
$routes->post('renameWishlist', 'UserController::renameWishlist');
$routes->post('createWishlist', 'UserController::createWishlist');
$routes->post('deleteWishlist', 'UserController::deleteWishlist');
$routes->post('deleteWishlistItem', 'UserController::deleteWishlistItem');
$routes->post('markWishlistFulfilled', 'UserController::markWishlistFulfilled');
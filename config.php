<?php
define('BASEURL', $_SERVER['DOCUMENT_ROOT'].'/hakstocks/');
define('CART_COOKIE', 'SBwi43UCkUwips9joo3');
define('CART_COOKIE_EXPIRE',time() + (86400 * 30));
define('TAXRATE',0.07);

define('CURRENCY','USD');
define('CHECKOUTMODE','TEST'); // Change mode to live if choice is live.

if (CHECKOUTMODE == 'TEST') {
 	define('STRIPE_PRIVATE', 'private_key');
 	define('STRIPE_PUBLIC', 'public_key');
}

if (CHECKOUTMODE == 'LIVE') {
 	define('STRIPE_PRIVATE', 'private_key');
 	define('STRIPE_PUBLIC', 'public_key');
}  

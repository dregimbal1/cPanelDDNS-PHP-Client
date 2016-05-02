<?php
/**
 * Updates Type 'A' Record in your cPanel DNS settings for specified domain
 *
 * PHP version 5.3
 *
 * LICENSE: 
 *	
 * @package	    DDNSUpdater
 * @author 	    David Regimbal <david@regimbal.me>
 * @copyright 	2016 David Regimbal
 * @version 	0.0.1
 * @link	    http://github.com/regimbal93
 *
 */

$config = array(
   'cpanel' => array(
	'host' 		=> '',		# cPanel host address
	'port' 		=> '2083',	# default
	'account' 	=> '',		# same as your username most likely					
	'user'		=> '',		# cPanel username
	'password'	=> ''		# cPanel password
   ),
   'settings' => array(
	'domain'	=> '',		# the record's domain.
	'name'		=> '',		# subdomain if applicable
	'ttl'		=> '',		# the record's time to live, in seconds.
	'type'		=> 'A'		# 'A' Record
   ),
);

// Set to true to view log output
$debug = false;

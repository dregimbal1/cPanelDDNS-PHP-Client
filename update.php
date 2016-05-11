<?php
/**
 * Updates Type 'A' Record in your cPanel DNS settings for specified domain
 *
 * PHP version 5.3
 *
 * LICENSE: 
 *	
 * @package 		DDNSUpdater
 * @author 			David Regimbal <david@regimbal.me>
 * @copyright 	2016 David Regimbal
 * @version 		0.0.2
 * @link				http://github.com/regimbal93
 *
 */

include 'Core/ddnsupdater.php';

$dns = new DDNSUpdater();

$dns->update();
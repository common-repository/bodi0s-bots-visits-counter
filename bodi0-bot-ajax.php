<?php
/*
Plugin`s AJAX function calls
Author: Budiony Damyanov
Author URI: mailto:budiony@gmail.com
Email: budiony@gmail.com
Version: 0.9.0
License: GPL2

Copyright 2024  bodi0  (email : budiony@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Typical headers
header('Content-Type: text/html');

//Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// Path to WordPress's wp-load.php file
require_once('../../../wp-load.php');

// Now the WordPress environment is loaded, and you can use WP functions

// Load the text domain for translations
load_plugin_textdomain('bodi0-bot-counter', false, dirname(plugin_basename(__FILE__)) . '/languages');

//Action via 'GET'
$action = (isset( $_GET['action'])) ? $_GET['action'] : '';

//A bit of security
if(!in_array($action, array( 'get_location_info', 'get_pagerank_google', 'get_pagerank_alexa', 'get_pagerank_statscrop' ))) {
	_e("Invalid AJAX action.", "bodi0-bot-counter"); 
	exit();
}

//The AJAX action is OK, let`s switch it
else {  
	switch ($action) {
		case 'get_location_info' :
		get_location_info();
		break;
		
		case 'get_pagerank_google' :
		$url = isset($_GET['url'])? $_GET['url'] : '';
		get_pagerank_google($url);
		break;
		
		case 'get_pagerank_alexa' :
		$url = isset($_GET['url'])? $_GET['url'] : '';
		get_pagerank_alexa($url);
		break;
		
		case 'get_pagerank_statscrop' :
		$url = isset($_GET['url'])? $_GET['url'] : '';
		get_pagerank_statscrop($url);
		break;
		
		default : 
		break;
		}
 }

// Get and display location information via AJAX
function get_location_info () {
	require(dirname(__FILE__)."/class.geolocation.php");
	/*******************************/
	$html = '';
    $loc_data = array();
	//Class instance
	$geo = new geo_location;
	//Get IP
	$geo->remote = isset($_GET['ip'])? $_GET['ip'] : '';
	//The info
	echo '<h4>' .__("Location info", "bodi0-bot-counter") . '</h4>';
    
	//If is valid IP address
	if (filter_var($geo->remote, FILTER_VALIDATE_IP )) {
        $json = json_decode($geo->getLocationInfoByIpAPI(), true);
        //
        if(is_array($json) && isset($json['ip'])) {
            $loc_data['ip'] = $json['ip'];
            $loc_data['country_name'] = $json['location']['country'] ?? 'N/A';
            $loc_data['country_code2'] = $json['location']['country_code'] ?? 'N/A';
            $loc_data['continent'] = $json['location']['continent'] ?? 'N/A';
            $loc_data['city'] = $json['location']['city'] ?? 'N/A';
            $loc_data['zip'] = $json['location']['zip'] ?? 'N/A';
            $loc_data['isp'] = $json['asn']['org'] ?? 'N/A';
            $loc_data['domain'] = $json['asn']['domain'] ?? 'N/A';
            $loc_data['descr'] = $json['asn']['descr'] ?? 'N/A';
        }
        
		foreach ($loc_data as $key=>$value) {			
			echo strip_tags($key.': '.(empty($value) ? 'N/A' : '<strong>'.strip_tags($value).'</strong>'), '<strong>')."<br/>"; 
		}
	}
	else {
		$ip = isset($_GET['ip']) ? $_GET['ip'] : "";
        _e(sprintf("Invalid IP address: %s", esc_html($ip)), "bodi0-bot-counter");
        exit;
	}
}

// Get Google`s page rank for given URL
function get_pagerank_google($url) {
	// Include the class
	require(dirname(__FILE__)."/class.google-pagerank.php");
	//If is valid URL
	if (filter_var($url, FILTER_VALIDATE_URL )) {
		// Display result
		$google = new get_google_pagerank;
		echo $google->pagerank($url);
	}
	else _e("Invalid URL", "bodi0-bot-counter");
}

// Get Google`s page rank for given URL
function get_pagerank_alexa($url) {
	// Include the class
	require(dirname(__FILE__)."/class.alexa-pagerank.php");
	//If is valid URL
	if (filter_var($url, FILTER_VALIDATE_URL )) {
		// Display result
		$alexa = new get_alexa_rank;
		//Get the rank for the domain paulund.co.uk
		echo $alexa->get_rank($url);
	}
	else _e("Invalid URL", "bodi0-bot-counter");
}

// Get Stastcrop page rank for given URL
function get_pagerank_statscrop($url) {
	// Include the class
	require(dirname(__FILE__)."/class.statscrop-pagerank.php");
	//If is valid URL
	$url = str_replace(array("http://","https://","ftp://", "ftps://","127.0.0.1","localhost","/"), "", $url);
		// Display result
		$statscrop = new get_statscrop_rank;
		//Get the rank for the domain 
		$statscrop->get_rank($url);
}

//EOF
?>
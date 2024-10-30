<?php
/*
Description: Get location data based on IP address, using geoplugin.net website API
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

if(!class_exists('geo_location')) {
    class geo_location {
        //
        var $ip = '';
        var $client = '';
        var $forward = '';
        var $remote = '';
        //
        function __construct() {
            $this->client = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
            $this->forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
            $this->remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }
        //
        function __destruct() {
            foreach($this as $key => $value) {
                unset($this->$key);
            }
        }
        
        /*
	   Return array of city and country from json decoded file at api.ipapi.is
	   */
        function getLocationInfoByIpAPI() {
            $ip_data = NULL;            
            try {
                $ip_data = @file_get_contents("https://api.ipapi.is/?q=" . $this->remote);
            } catch(Exception $e) {
                echo $e->getMessage();
            }
            return $ip_data;
        }
    }
}

?>
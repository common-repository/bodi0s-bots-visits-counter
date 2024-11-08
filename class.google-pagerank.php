<?php
/*
Plugin`s Page rank function calls (using the Google`s toolbar queries)
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
if (!class_exists('get_google_pagerank')) {
class get_google_pagerank {

private function genhash ($url) {
	$hash = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
	$c = 16909125;
	$length = strlen($url);
	$hashpieces = str_split($hash);
	$urlpieces = str_split($url);
	for ($d = 0; $d < $length; $d++) {
		$c = $c ^ (ord($hashpieces[$d]) ^ ord($urlpieces[$d]));
		$c = (($c >> 23) & 0x1ff) | $c << 9;
 	}
 	$c = -(~($c & 4294967295) + 1);
 	return '8' . dechex($c);
}

public function pagerank($url) {
	$url = 'http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=' . $this->genhash($url) . '&features=Rank&q=info:' . urlencode($url);
	if(function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$out = curl_exec($ch);
		curl_close($ch);
	} 
	elseif (!function_exists('curl_init')) {
		$out = file_get_contents($url);
	}
	else {
		$out = 'N/A';
		}
	
	if(strlen($out) > 0) {
		
		//Format the results from Google
		$out_array = explode(":", trim( substr( strip_tags($out),0,10) ) );
		return !empty($out_array[2]) ? $out_array[2] : ($out_array[0]);
	} 
	else {
		return 'N/A';
	}
}

}
}
/* Usage
/* The most popular websites have a PageRank of 10. The least have a PageRank of 0

$google = new get_google_pagerank;
echo $google->pagerank('http://www.agrifoodresults.eu/');
*/

?>
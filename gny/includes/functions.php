<?php

    require_once('HTTP/Request.php');
    require_once('cache.php');
    require_once 'evel.php';
    require_once 'countries.php';
	
    //Send a text email
    function send_text_email($to, $from_name, $from_email, $subject, $body){

    evel_send(array(
            '_body_' => $body,
            'To' => $to,
            'From' => array($from_email, $from_name),
            'Subject' => $subject,
        ), $to);
	    
    }

	//Valid email address?
    function valid_email ($string) {
        $valid = false;
    	if (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
    		'@'.
    		'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
    		'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $string)) {
    		$valid = false;
    	} else {
    		$valid =  true;
    	}
    	
    	return $valid;
    }

	//Valid URL?
	function valid_url($url) {
		return preg_match("/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?/i", $url);
	}

	// Regexes from ms_make_clickable mostly, adjusted
	function raw_urls_to_links($text){
		$text = " $text ";
		$text = preg_replace('#(https?://[^\s<>{}()]+[^\s.,<>{}()])#i', '<a href="$1" rel="nofollow">$1</a>', $text);
		$text = preg_replace('#(\s)([a-z0-9\-]+(?:\.[a-z0-9\-\~]+){2,}(?:/[^ <>{}()\n\r]*[^., <>{}()\n\r])?)#i', 
			'$1<a href="http://$2" rel="nofollow">$2</a>', $text);

		$text = trim($text);
		return $text;
	}
	
	function new_lines_to_paragraphs($text){
	
		$return = "";
		$lines = explode("\n", $text);
		foreach($lines as $line) {
			if(trim($line) !=''){
				$return .= "<p>" . trim($line) . "</p>";
			}
		}
	
		return $return;
	}

	//is a postcode?
	function is_postcode ($postcode) {
		// See http://www.govtalk.gov.uk/gdsc/html/noframes/PostCode-2-1-Release.htm

		$in  = 'ABDEFGHJLNPQRSTUWXYZ';
		$fst = 'ABCDEFGHIJKLMNOPRSTUWYZ';
		$sec = 'ABCDEFGHJKLMNOPQRSTUVWXY';
		$thd = 'ABCDEFGHJKSTUW';
		$fth = 'ABEHMNPRVWXY';
		$num = '0123456789';
		$nom = '0123456789';
		$gap = '\s\.';	

		if (	preg_match("/^[$fst][$num][$gap]*[$nom][$in][$in]$/i", $postcode) ||
				preg_match("/^[$fst][$num][$num][$gap]*[$nom][$in][$in]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num][$gap]*[$nom][$in][$in]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num][$num][$gap]*[$nom][$in][$in]$/i", $postcode) ||
				preg_match("/^[$fst][$num][$thd][$gap]*[$nom][$in][$in]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num][$fth][$gap]*[$nom][$in][$in]$/i", $postcode)
			) {
			return true;
		} else {
			return false;
		}
	}

	//is a postcode?
	function is_partial_postcode ($postcode) {
		// See http://www.govtalk.gov.uk/gdsc/html/noframes/PostCode-2-1-Release.htm
		$fst = 'ABCDEFGHIJKLMNOPRSTUWYZ';
		$sec = 'ABCDEFGHJKLMNOPQRSTUVWXY';
		$thd = 'ABCDEFGHJKSTUW';
		$fth = 'ABEHMNPRVWXY';
		$num = '0123456789';

		if (	preg_match("/^[$fst][$num]$/i", $postcode) ||
				preg_match("/^[$fst][$num][$num]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num][$num]$/i", $postcode) ||
				preg_match("/^[$fst][$num][$thd]$/i", $postcode) ||
				preg_match("/^[$fst][$sec][$num][$fth]$/i", $postcode)
			) {
			return true;
		} else {
			return false;
		}
	}

	function pad_partial_postcode($partial_postcode){

		$partial_postcode = strtolower($partial_postcode);
		if($partial_postcode == "sw1" || $partial_postcode == "ec1"){
			$return = $partial_postcode . "a 1aa";
		}elseif($partial_postcode == "sw9"){
			$return = $partial_postcode . "8jx";		
		}else{
			$return = $partial_postcode . " 1aa";
		}
				
		return $return;
		
	}

	//iszip code
	function is_zipcode ($zipcode) {

		if (preg_match("/[0-9]{5}/", $zipcode)) {
			return true;	
		} else {
			return false;
		}
	}
	
	//Does a stig look like a long/lat value
	function is_longlat ($longlat) {

		$return = true;
		$split = explode(",", $longlat);
		if(sizeof($split) != 2){
			$return = false;
		}else{
			if (!is_numeric(str_replace("-", "", $split[0]))){
				$return = false;
			}
			if (!is_numeric(str_replace("-", "", $split[1]))){
				$return = false;
			}
		}
		
		return $return;

	}

	//clean postcode (adds space and makes uppcase)
	function clean_postcode ($postcode) {

		$postcode = str_replace(' ','',$postcode);
		$postcode = strtoupper($postcode);
		$postcode = trim($postcode);
		$postcode = preg_replace('/(\d[A-Z]{2})/', ' $1', $postcode);
	
		return $postcode;

	}

	//clean postcode (adds space and makes uppcase)
	function postcode_to_district ($postcode) {

		$reg = array();
		$postcode = trim($postcode);
		preg_match('/^(.+?)([0-9][a-z]{2})$/',$postcode, $reg);
	
		$clean_postcode = trim($reg[1]);
		$clean_postcode = strtoupper($clean_postcode);

		return $clean_postcode;

	}

	// Get a postcode's location
	function get_postcode_location($zip, $country){

		if ($country == 'UK') {
			$data = json_decode(@file_get_contents('http://mapit.mysociety.org/postcode/' . (is_partial_postcode($zip) ? 'partial/' : '') . rawurlencode($zip)), true);
			if (!$data)
				return false;
			return array(
				0 => $data['wgs84_lon']+0,
				1 => $data['wgs84_lat']+0
			);
		} else {
			$url = "http://maps.google.com/maps/geo?key={key}&output=csv&q={zip},+{country}";
			$url = str_replace('{key}', GOOGLE_MAPS_KEY, $url);
			$url = str_replace('{zip}', urlencode($zip), $url);
			$url = str_replace('{country}', urlencode($country), $url);

			$data = safe_scrape_cached($url);
	 		return process_google_geocoder($data);
		}
	}

	/* Given a search string, try and coax out the country, state, etc. */
	function get_place_parts($s) {
		global $countries_name_to_code, $countries_code_to_name, $countries_name_to_statecode, $countries_statecode_to_name;
		$s = explode(',', $s);
		$parts = array();
		$last = trim(end($s));
		if (isset($countries_name_to_code[strtolower($last)])) {
			$parts['country'] = $countries_name_to_code[strtolower($last)];
			array_pop($s);
		} elseif (isset($countries_name_to_statecode['US'][strtolower($last)])) {
			$parts['state'] = $countries_name_to_statecode['US'][strtolower($last)];
			$parts['country'] = 'US';
			array_pop($s);
		} elseif (isset($countries_statecode_to_name['US'][strtoupper($last)])) {
			$parts['state'] = strtoupper($last);
			$parts['country'] = 'US';
			array_pop($s);
		} elseif (isset($countries_code_to_name[strtoupper($last)])) {
			$parts['country'] = strtoupper($last);
			array_pop($s);
		} elseif (strtoupper($last) == 'UK') {
			$parts['country'] = 'GB';
			array_pop($s);
		}

		$last = trim(end($s));
		if ($parts['country'] && isset($countries_name_to_statecode[$parts['country']][strtolower($last)])) {
			$parts['state'] = $countries_name_to_statecode[$parts['country']][strtolower($last)];
			array_pop($s);
		} elseif ($parts['country'] && isset($countries_statecode_to_name[$parts['country']][strtoupper($last)])) {
			$parts['state'] = strtoupper($last);
			array_pop($s);
		}

		if (count($s) >= 2) {
			$parts['street'] = trim($s[0]);
			$parts['place'] = trim($s[1]);
		} elseif (count($s)) {
			$parts['place'] = trim($s[0]);
		}
		return $parts;
	}

	// Get a location
	function get_place_location($parts) {

		//try the city search (for outside the us)
		$url = 'http://maps.google.com/maps/geo?key=' . GOOGLE_MAPS_KEY . '&output=csv&q=';
		$out = array();
		if (isset($parts['street'])) $out[] = urlencode($parts['street']);
		if (isset($parts['place'])) $out[] = urlencode($parts['place']);
		if (isset($parts['state'])) $out[] = urlencode($parts['state']);
		$out[] = urlencode($parts['country']);
		$url .= join(',+', $out);

		$data = safe_scrape_cached($url);
		return process_google_geocoder($data);
	}
	
	// Process results from Google Maps API geocoder
	function process_google_geocoder($data) {
		$data = explode(',', $data);
		$return[0] = $data[3] + 0;
		$return[1] = $data[2] + 0;
		return $return;
	}

	//Tiny url
    function tiny_url($url,$length=30){

    	// make nasty big url all small
    	if (strlen($url) >= $length){
    		$tinyurl = @file ("http://tinyurl.com/api-create.php?url=$url");
    		
    		if (is_array($tinyurl)){
    			$tinyurl = join ('', $tinyurl);
    		} else {
    			$tinyurl = $url;
    		}
    	} else {  
    		$tinyurl = $url; 
    	}

    	return $tinyurl;
    }

    //Google maps url
    function googlemap_url_from_postcode($postcode, $zoom = 15){
        $postcode = strtolower(str_replace(" ", "+", $postcode));
        return "http://maps.google.co.uk/maps?q=$postcode&z=$zoom";
    }

	//Scarpe a url and cache it
    function safe_scrape_cached($url, $cache_time = CACHE_TIME){

		$cache = cache::factory($cache_time);

		$cached = $cache->get($url);
		if (isset($cached) && $cached !== false) {
			return $cached;
		}else{
			$page = safe_scrape($url);
		    $cache->set($url, $page, "safe_scrape");	
			return $page;
		}
		
	}	

	//Scrape a url
    function safe_scrape($url){
        $page = "";
        for ($i=0; $i < 3; $i++){ 
            if($page == false){
                 if (SCRAPE_METHOD == "PEAR"){
                     $page = scrape_page_pear($url);
                 }else{
                     $page = scrape_page_curl($url);         
                 }
            }   
        }
        return $page;
    }
    
	//scrape by pear
    function scrape_page_pear($url){
        $page = "";
        $request = new HTTP_Request($url, array("method" => "GET", "allowRedirects" => true));
        $request->sendRequest();
        $page = $request->getResponseBody();
        
        return $page;

    }
    
	//scrape by curl
    function scrape_page_curl($url) {
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
		return curl_exec($ch);
	}

	function safe_string($object){
		return $object . '';
	}

	//Redirect page to a URL
	function redirect ($url){
	    header("Location: $url");
		exit;
	}

	//Throw 404
	function throw_404(){
		header("HTTP/1.0 404 Not Found");
		include("the404.php");
		exit;
	}

	//Format a string for a url
	function format_string_for_url($string) {
		$string = str_replace(
			array('&', "'", ' ', '/'),
			array('and', '', '_', '_'),
			$string
		);
		$string = strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $string));
		return $string;
	}

	///////////////////////////////
	// Cal's functions from
	// http://www.iamcal.com/publish/article.php?id=13

	// Call this with a key name to get a GET or POST variable.
	function get_http_var ($name, $sanitize=true, $default=null){
		//global $HTTP_GET_VARS, $HTTP_POST_VARS;
		if (array_key_exists($name, $_POST)) {
			return mk_utf8(clean_var($_POST[$name], $sanitize));
		}
		if (array_key_exists($name, $_GET)) {
			return mk_utf8(clean_var($_GET[$name], $sanitize));
		}
	
		return $default;
	}

	/**
	 * Take a string and make it utf8 if it isn't already (it should be though)
	 */
	function mk_utf8($string) {
		return (is_utf8($string) ? $string : utf8_encode($string));
	}

	function clean_var ($a, $sanitize=true){
		$out = (ini_get("magic_quotes_gpc") == 1) ? recursive_strip($a) : $a;
	
		if ($sanitize) {
			// We want to filter out all kinds of nasty shit that a user might
			// have thrown at us.
		
			// Filtering commented out at the moment as it seems to be causing
			// problems on the dev site.
			//return filter_var($out, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);	
			return $out;
		}
		else {
			return $out;
		}
	}

	function recursive_strip ($a){
		if (is_array($a)) {
			while (list($key, $val) = each($a)) {
				$a[$key] = recursive_strip($val);
			}
		} else {
			$a = StripSlashes($a);
		}
		return $a;
	}

	function is_utf8($str) {
		if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
			return true;
		} else {
			return false;
		}
	}

	//Approximate zoom level for a google map based on 2 longitude values
	function approximate_gmap_zoom($long_left, $long_right){

		$width = $long_left - $long_right;
		if($width < 0){
			$width = $width / -1;
		}
	
		$width = floor($width);

		$zoom = 0;
		if($width <= 1){
			$zoom = 8;
		}elseif($width <= 3){
			$zoom = 7;
		}elseif($width <= 5){
			$zoom = 6;
		}elseif($width <= 15){
			$zoom = 5;	
		}else{
			$zoom = 3;
		}

		return $zoom;
	}
	
	function vardump($bla) {
		print '<pre style="text-align: left">';
		var_dump($bla);
		print '</pre>';
	}
	// Format a date to mysql format
	function mysql_date($date){
	    return date("Y-m-d H::i:s", $date);
	}

	// very inacurate way of converting distance to a point on latitude	
	function distance_to_latitude($distance_km){
		$km_per_degree = 111;
		return $latitude + ($distance_km / $km_per_degree);
	}
	
	// very inacurate way of converting distance to a point on longitude
	function distance_to_longitude($distance_km){
		$km_per_degree = 111.321;
		return $distance_km / $km_per_degree;
	}

	//Remove Line Breaks
	function remove_line_breaks($string){
		return str_replace("\n", "", str_replace("\r\n","", $string));
	}

	function get_random_numbers($return_size, $start, $end) {
	     $random_numbers = array();
	     $rand_temp = array();

	     while(count($random_numbers) < $return_size) {
	        $temp = rand($start, $end);
	        if(!isset($rand_temp[$temp])) {
	           $rand_temp[$temp] = true;
	           $random_numbers[] = $temp;
	        }
	     }

	     return $random_numbers;
	  }

		
?>

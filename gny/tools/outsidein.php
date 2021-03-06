#!/usr/bin/php5
<?php

	require_once(dirname(__FILE__) .'/../conf/general');  
	require_once(dirname(__FILE__) .'/../includes/init.php');

	//thread count
	$thread_count_tracker = array();
	$minimum_thread_count = 20;

	//get all cities list
	$usa_url = "http://outside.in/us_country_map";

	$usa_html = safe_scrape($usa_url);

	//get list of citoes
	$city_regex = "/<a href=\"http:\/\/outside.in.*?_[A-Z][A-Z]\">/";

	preg_match_all($city_regex, $usa_html, $city_matches, PREG_PATTERN_ORDER);

	//loop though the cities
	foreach ($city_matches[0] as $city_match) {

		//get city name and URL
		$city_url = str_replace('<a href="', '', $city_match);
		$city_url = str_replace('">', '', $city_url);		
		$city_name = trim(str_replace("http://outside.in","", $city_url));
		$city_name = str_replace("/", "", $city_name);

		//grab neighbourhood urls
		$city_html = safe_scrape($city_url);
		$hood_regex = "/<a href=\"http:\/\/outside.in.*?_[A-Z][A-Z]\">/";
		preg_match_all($hood_regex, $city_html, $hood_matches, PREG_PATTERN_ORDER);		
		
		//loop though neighbourhoods
		foreach ($hood_matches[0] as $hood_match) {
			if(strpos($hood_match, $city_name) > 0){
				
				//get neighbourhood URL, discussion url and name
				$hood_url = str_replace('<a href="', '', $hood_match);
				$hood_url = str_replace('">', '', $hood_url);		
				$hood_name = trim(str_replace("http://outside.in","", $hood_url));
				$hood_name = str_replace("/", "", $hood_name);
				
				// if this is a neighbourhood rather than a full city, then remove the city name form the end of the hood name
				if($city_name != $hood_name){
					$hood_name = str_replace($city_name, "", $hood_name);				
				}

				$hood_discussion_url = $hood_url . "/discussions";
				
				//check if the board has any threads

				$board_html = safe_scrape($hood_discussion_url);
				$board_html = str_replace("\n", "", str_replace("\r\n","", $board_html));

				$post_count_regex = "/[0-9]*?threads/";
				preg_match_all($post_count_regex, $board_html, $post_count_matches, PREG_PATTERN_ORDER);						

				$thread_count = $post_count_matches[0][0];

				array_push($thread_count_tracker, str_replace("threads", "", $thread_count));

				/*
				$no_threads = strpos($board_html, "No discussions") > 0;
				*/
				
				if($thread_count >= $minimum_thread_count){

					//grab the neighbourhood page
					$hood_html = safe_scrape($hood_url);
				
					//get long and lat
					$location_regex = "/GLatLng\(.*?,.*?\)/";				
					preg_match_all($location_regex, $hood_html, $location_matches, PREG_PATTERN_ORDER);						
			
					//get bounding box
					$latlong_bottom_left = array();
					$latlong_top_right = array();				

					$latlong_bottom_left = split(",", str_replace(")", "", str_replace("GLatLng(", "", $location_matches[0][0])));
					$latlong_top_right = split(",", str_replace(")", "", str_replace("GLatLng(", "", $location_matches[0][1])));				

					//make a new group and save it
					$group = factory::create('group');
					$group->name = trim(str_replace("_", " ", $hood_name)) . " (outside.in)";
					$group->byline = "Comments and questions about all things " . str_replace("_", " ", $hood_name);
					$group->description = "A community discussion board for " . str_replace("_", " ", $hood_name) . ". Talk with people around you, ask questions, give answers, rant, weigh in, and so forth.";
					$group->tags = "outside.in, discussion, " . strtolower(str_replace("_", " ", $hood_name));
					$group->category_id = 1;
					$group->involved_type = "link";
					$group->involved_link = $hood_discussion_url;
					$group->created_name = "mySociety";
					$group->created_email = "richard@mysociety.org";
					$group->confirmed = true;
					$group->long_bottom_left = $latlong_bottom_left[1];
					$group->lat_bottom_left = $latlong_bottom_left[0];
					$group->long_top_right = $latlong_top_right[1];
					$group->lat_top_right = $latlong_top_right[0];
					if($city_name != $hood_name){
						$group->zoom_level = 13;
					}else{
						$group->zoom_level = 10;						
					}
					$group->long_centroid = ($latlong_bottom_left[1] + $latlong_top_right[1]) / 2;
					$group->lat_centroid = ($latlong_bottom_left[0] + $latlong_top_right[0]) / 2;

					//check if it's already been added
					$search = factory::create('search');
					$result = $search->search_cached('group', array(array('involved_link', '=', $hood_discussion_url)));

					if(sizeof($result) == 0){
						//set url and save
						$group->set_url_id();
				
						if($group->insert()){
							print "saved: " . $hood_name . "\n";
						}else{
							print "failed: " . $hood_name . "\n";
						}
					}else{
						print "duplicate: " . $hood_name. "\n";
					}
				}else{
					print "no threads: " . $hood_name . "\n";
				}
			}
		}

	}

	//print distribution of posts
	/*
	foreach ($thread_count_tracker as $thread_count) {
		print $thread_count . ", ";
	}
	*/


?>
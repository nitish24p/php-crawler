<?php 

$to_crawl = "";
$c = array();
$base_url = parse_url($to_crawl , PHP_URL_HOST);
function get_links($url){
	global $c;
	$input = @file_get_contents($url);
	$regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	preg_match_all("/$regex/siU", $input, $matches);
	$base_url = parse_url($url , PHP_URL_HOST);
	$l = $matches[2];
	foreach ($l as $link) {
		// for SPA URLs
		if (strpos($link, "#")) {
			$links = substr($link, 0 , strpos($link, "#"));
		}
		// check if URLs start with a .
		if (substr($link , 0 , 1) == ".") {
			$link = substr($link, 1);
		}
		// check if http:// or https:// or mail id 
		if (substr($link, 0 , 7) == "http://") {
			$link = $link;
		}
		else if (substr($link, 0 , 8) == "https://") {
			$link = $link;
		}
		else if (substr($link, 0 , 2) == "//") {
			$link = substr($link, 2);
		}
		else if (substr($link, 0 , 1) == "#") {
			$link = $url;
		}
		else if (substr($link, 0 , 7) == "mailto:") {
			$link = "[".$link."]";
		}
		else {
			if (substr($link, 0 , 1) != "/") {
				$link = $base_url."/".$link;
			}
			else{
				$link = $base_url.$link;
			}
		}


		if (substr($link, 0 , 7) != "http://"  &&  
			substr($link, 0 , 8) != "https://" && 
			substr($link, 0 , 1) != "[") {
			if (substr($url , 0 , 8) == "https://") {
				$link = "https://".$link;
			}
			else
			{
				$link = "http://".$link;
			}
		}

		if (!in_array($link, $c)) {
			array_push($c, $link);
		}
	}
	
}

get_links($to_crawl);

foreach ($c as $page) {
	get_links($page);
}

foreach ($c as $page) {
	if ($base_url == parse_url($page , PHP_URL_HOST) || "www".$base_url == parse_url($page , PHP_URL_HOST)) {
		echo $page."\n";
	}
	
}

echo count($c)." results found"."\n";

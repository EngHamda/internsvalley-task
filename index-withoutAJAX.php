<?php
/**
 * using while loop for get all pages
 * if URL content is false(ex. url ="www.google.com"), 
 * 			print to user "Can't get URL Content" and EXIT
 * 			//Todo: if page > 0 add to array
 * get all <a> element in page with attribute (data-gtm-id), and its value start with "search-resultlist-searchresultspageitem"
 * check if href value for this element match exp '#^/[A-Za-z]+/\d+$#' (ex.'/mieten/108983794')
 * check if the links of current page and previous are identical 
 *			//==  TRUE if tw arrays have the same key/value pairs.
 * 			//=== TRUE if two arrays have the same key/value pairs in the same order and of the same types.
 * 
 */
ini_set('max_execution_time', 0);

include('simple_html_dom.php');
$links =[];
$url = "https://www.homegate.ch/mieten";
$uriPage = "/immobilien/kanton-zuerich/trefferliste";
$elementIdentity = 'a[data-gtm-id^="search-resultlist-searchresultspageitem"]';
$currentPageNumber = 1;
$previousPageNumber = 0;

while ($previousPageNumber < $currentPageNumber) {
	$htmlContent = @file_get_html($url.$uriPage."?ep=".$currentPageNumber);
	if($htmlContent === FALSE){
		//if page not exist
		die("Can't get URL Content");
	}//endIF page not exist
	//get links from this page

	$linkElements = $htmlContent->find($elementIdentity);
	$pageLinks = [];
	foreach ($linkElements as $key => $element) {
		$link = $element->href;
		if(preg_match('#^/[A-Za-z]+/\d+$#', $link)){
			$pageLinks[] = $link;
		}
		unset($linkElements[$key]);		
	}
	if($previousPageNumber > 0 && @$links['PageNumber '.$previousPageNumber] == $pageLinks){
		$previousPageNumber = $currentPageNumber ;
	} else{
		$links['PageNumber '.$currentPageNumber] = $pageLinks;
		$currentPageNumber++;
		$previousPageNumber++;
	}
}

echo "<pre>";
var_dump($links,$previousPageNumber,$currentPageNumber);
echo "</pre>";
// //print links
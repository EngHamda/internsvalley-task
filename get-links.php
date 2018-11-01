<?php
/**
 * using while loop for get all pages in another script
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
include('simple_html_dom.php');
//todo:try-catch

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	return json_encode(["error"=>true, "msg"=>"Can't get response"]);
}
//get post data
//Todo: check page number exist, and is int 
$currentPageNumber = $_POST["currentPageNumber"];
$taskType =$_POST['taskType'];

if($taskType =='simple-task'){
	$expression = '#^/[A-Za-z]+/\d+$#';
	echo getTaskResponse($taskType,$expression, $currentPageNumber);
} elseif ($taskType=='advanced-task') {
	$expression="";
	$url = "https://www.newhome.ch";
	$uriPage = "/de/kaufen/suchen/haus_wohnung/kanton_zuerich/liste.aspx?p=";
	$elementIdentity = 'a[href^="https://www.newhome.ch/de/kaufen/immobilien/haus/"]';		
	echo getTaskResponse($taskType, $expression, $currentPageNumber, $url, $uriPage, $elementIdentity);
} 

function getTaskResponse($pTaskType,$pExpression, $pPageNumber=1, $pUrl = "https://www.homegate.ch", $pUriPage = "/mieten/immobilien/kanton-zuerich/trefferliste?ep=", $pElementIdentity = 'a[data-gtm-id^="search-resultlist-searchresultspageitem"]')
{
	$links =[];
	$htmlContent = @file_get_html($pUrl.$pUriPage.$pPageNumber);
	if($htmlContent === FALSE){
		//if page not exist
		return json_encode(["error"=>true, "msg"=>"Can't get URL Content"]);
	}//endIF page not exist

	//get links from this page
	$linkElements = $htmlContent->find($pElementIdentity);
	
	foreach ($linkElements as $key => $element) {
		$link = $element->href;
		if($pTaskType =='simple-task'){
			if(preg_match($pExpression, $link)){
				$links[] = $pUrl.$link;
			}
		} elseif ($taskType=='advanced-task') {
			$links[] = $link;
		} 
		unset($linkElements[$key]);		
	}//endForeach

	//send links to front-end
	return json_encode([
		"error"=>false, "msg"=>"success", 
		"content"=>["links"=>$links, 'currentPageNumber'=>$pPageNumber]]);
}

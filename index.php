<?php 
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	
	$companyValue = $json->result->parameters->companyName;
	$searchValue = $json->result->parameters->searchType;
	$officeLocationValue = $json->result->parameters->officeLocation;
	$consultantNameValue = $json->result->parameters->consultantName;
	$managerNameValue = $json->result->parameters->managerName;
	
	// only process when consultant contains a value
	if (!(is_null($consultantNameValue) || empty($consultantNameValue)) )
	{
		//if spaces, replace with +
		if (preg_match('/\s/',$consultantNameValue)) 
		{
			$consultantNameValue = preg_replace('/\s+/', ' ', $consultantNameValue);
		}
		$searchQuery .= "&name=" . rawurlencode(strtoupper($consultantNameValue));
	}
	
	// only process when company contains a value
	if (!(is_null($companyValue) || empty($companyValue)) )
		
	{
		$searchQuery .= "&company=" . rawurlencode(strtoupper($companyValue));
	}
		
	// only process when office location contains a value
	if (!(is_null($officeLocationValue) || empty($officeLocationValue)) )
	{
		$newLocatValue = array();
		foreach( $officeLocationValue as $value ) {
		
			//if spaces, replace with +
			if (preg_match('/\s/',$value)) 
			{
				$newLocatValue[] = preg_replace('/\s+/', ' ', $value);
			}
			else 
			{
				$newLocatValue[] = $value;
			}
		}
		$searchQuery .= "&office=" . rawurlencode(strtoupper(implode($newLocatValue, "-")));
	}
	
	// only process when manager contains a value
	if (!(is_null($managerNameValue) || empty($managerNameValue)) )
	{
		//if spaces, replace with +
		if (preg_match('/\s/',$managerNameValue)) 
		{
			$managerNameValue = preg_replace('/\s+/', ' ', $managerNameValue);
		}
		$searchQuery .= "&manager=" . rawurlencode(strtoupper($managerNameValue));
	}
	
	// only process when search contains a value
	if (!(is_null($searchValue) || empty($searchValue)) )
	{
		$newSearchValue = array();
		foreach( $searchValue as $value ) {
		
			//if spaces, replace with +
			if (preg_match('/\s/',$value)) 
			{
				$newSearchValue[] = preg_replace('/\s+/', ' ', $value);
			}
			else 
			{
					$newSearchValue[] = $value;
			}
		}
		$searchQuery .= "&search=" . rawurlencode(strtoupper(implode($newSearchValue, "-")));
	}
	$searchStr = substr($searchQuery, 1);
	$linkAddr = "https://codex.dialoggroup.biz/voice?" . $searchStr;
	
	//consume the link
	$client = curl_init($linkAddr);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
	
	//get response from resource
	$clientResponse = curl_exec($client);
	
	$messages=[];
	
	// Building Card
	array_push($messages, array(
		"type"=> 1,
		"title"=> "Dialog Codex Search Link",
		"subtitle"=> "Please log into Dialog Codex to find out the details of your search.",
		"formattedText"=> "Please log into Dialog Codex to find out the details of your search.",
		"imageUrl"=> "https://apaia-chatbot-webhook.herokuapp.com/app-logo.png",
		"buttons"=> [
			[
			  "text"=> "Dialog Codex Click Here",	
			  "postback"=> $linkAddr
			] 
		]
	));
	
	$response=array(
		"source" => "Webhook for Dialog Codex",
		"speech" => "This is your link: Please log into Dialog Codex to find out the details of your search.",
		"messages" =>  $messages,
		"displayText" => "This is your link: Please log into Dialog Codex to find out the details of your search.",
		"contextOut" => array()
	);
	
	exec('node ./launchBrowser.js', $output);
	echo json_encode($response);
	
}
else
{
	echo "Method not allowed";
	
}
?>
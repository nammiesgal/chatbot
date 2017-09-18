<?php 

	require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
	use Twilio\Rest\Client;
	use Twilio\Http\CurlClient;
	
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
		
		$linkAddr = "https://codex.dialoggroup.biz/?mode=voice";
		
		//&name=pete+ryan&company=dialog&office=south+brisbane&manager=alec+begley&search=.net-python-project+management
		
		// only process when consultant contains a value
		if (!(is_null($consultantNameValue) || empty($consultantNameValue)) )
		{
			//if spaces, replace with +
			if (preg_match('/\s/',$consultantNameValue)) 
			{
				$consultantNameValue = preg_replace('/\s+/', '+', $consultantNameValue);
			}
			$linkAddr .= "&name=" . $consultantNameValue;
		}
		
		// only process when company contains a value
		if (!(is_null($companyValue) || empty($companyValue)) )
			
		{
			$linkAddr .= "&company=" . $companyValue;
		}
			
		// only process when office location contains a value
		if (!(is_null($officeLocationValue) || empty($officeLocationValue)) )
		{
			$linkAddr .= "&office=";
			$newLocatValue = array();
			foreach( $officeLocationValue as $value ) {
			
				//if spaces, replace with +
				if (preg_match('/\s/',$value)) 
				{
					$newLocatValue[] = preg_replace('/\s+/', '+', $value);
				}
				else 
				{
					$newLocatValue[] = $value;
				}
			}
			$linkAddr .= implode($newLocatValue, "-");
		}
		
		// only process when manager contains a value
		if (!(is_null($managerNameValue) || empty($managerNameValue)) )
		{
			//if spaces, replace with +
			if (preg_match('/\s/',$managerNameValue)) 
			{
				$managerNameValue = preg_replace('/\s+/', '+', $managerNameValue);
			}
			$linkAddr .= "&manager=" . $managerNameValue;
		}
		
		// only process when search contains a value
		if (!(is_null($searchValue) || empty($searchValue)) )
		{
			$linkAddr .= "&search=";
			$newSearchValue = array();
			foreach( $searchValue as $value ) {
			
				//if spaces, replace with +
				if (preg_match('/\s/',$value)) 
				{
					$newSearchValue[] = preg_replace('/\s+/', '+', $value);
				}
				else 
				{
						$newSearchValue[] = $value;
				}
			}
			$linkAddr .= implode($newSearchValue, "-");
		}
		
		//consume the link
		$client = curl_init($linkAddr);
		curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
		
		//get response from resource
		$clientResponse = curl_exec($client);
		
		$accountSid = "AC08d095cb250390a8bb04bf5c36e749a6";
		$authToken = "53b7781098b8f71fb1dba914b5c9b5ad";
		$client = new Client($accountSid, $authToken);
		$curlOptions = [ CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
		$client->setHttpClient(new CurlClient($curlOptions));

		//build sms messages
		$fromNumber = "+61436414915";
		$toNumber = "+61400113044";
		$msg = "Here is your Dialog Codex search link: " . $linkAddr;
		
		try 
		{
			$message = $client->messages->create(
				$toNumber,
				array(
					'from' => $fromNumber,
					'body' => $msg
				)
			);
		}
		catch(\Exception $ex) 
		{
			print "Twilio error: " . $ex->getMessage();
		}

		$response=array(
			"source" => "Webhook for Dialog Codex",
			"speech" => "This is your link: Please log into Dialog Codex to find out the details of your search.",
			"messages" =>  $messages,
			"displayText" => "This is your link: Please log into Dialog Codex to find out the details of your search.",
			"contextOut" => array()
		);
	
		echo json_encode($response);
	}
	else
	{
		echo "Method not allowed";
	}
?>	
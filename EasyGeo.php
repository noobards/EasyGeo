<?php
class EasyGeo
{	
	private $searchTerm;
	private $callMethod;
	private $url;	

	// the function called by the user
	public function geocodeTextAddress($address)
	{	
		if(function_exists('curl_version')) // check if CURL is enabled
		{
			$this->callMethod = "curl";
		}
		else if(function_exists('file_get_contents')) // check if file_get_contents is enabled
		{
			$this->callMethod = "fgc";
		}
		else
		{
			$this->showError("The library needs either cURL or file_get_contents to be enabled on the server. Both were found to be disabled. Please re-enable them and try again");
		}

		$this->searchTerm = $address;		
		$this->url = $this->generateGeocodeURL(urlencode($this->searchTerm));

		$result = $this->geocode();

		if(count($result) > 0) // at least something was returned from the service call
		{
			if($result['status'] == 'OK')
			{
				$results = $result['results'];
				$response = array();			
				foreach($results as $index=>$item)
				{
					foreach($item as $key=>$meta)
					{
						if($key == 'address_components')
						{
							foreach($meta as $array)
							{
								if($array['types'][0] == 'postal_code')
								{
									$response[$index]['zip_code'] = $array['long_name'];
								}
								else if($array['types'][0] == 'postal_code_suffix')
								{
									$response[$index]['zip_code_suffix'] = $array['long_name'];
								}
							}
						}
						else if($key == 'formatted_address')
						{
							$response[$index]['full_address'] = $meta;
						}
						else if($key == 'geometry')
						{
							$response[$index]['latitude'] = $meta['location']['lat'];
							$response[$index]['longitude'] = $meta['location']['lng'];
						}
						else if($key == 'place_id')
						{
							$response[$index]['place_id'] = $meta;
						}
					}
				}

				return array(
					'address_searched_for'	=>	$this->searchTerm,
					'places'				=>	$response
				);
			}
			else
			{
				if(isset($data['msg']))
				{
					$this->showError($data['msg']);
				}
				else
				{
					$this->showError("No results found for the address '".$this->searchTerm."'");
				}				
			}
		}
		else // service call (curl or fgc) was not initiated at all
		{
			$this->showError("Could not determine the call method (curl or fgc). Please contact the author stating the same!");
		}

	}

	private function geocode()
	{
		$data = array();
		if($this->callMethod == 'curl')
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->url);
   			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   			curl_setopt($ch, CURLOPT_VERBOSE, true);
   			$data = json_decode(curl_exec($ch), true);
   			if(curl_error($ch))
			{
			    $data['msg'] = curl_error($ch);
			    $data['status'] = "NOTOK";
			}   		
   			curl_close($ch);
		}
		else if($this->callMethod == 'fgc')
		{			
			$data = json_decode(@file_get_contents($this->url), true);
			if(count($data) == 0)
			{
				$this->showError("Could not complete the action. The URL called was :".$this->url);
			}
		}
		return $data;
	}


	// url for getting data for a textual address
	private function generateGeocodeURL($str)
	{
		return "http://maps.googleapis.com/maps/api/geocode/json?address=".$str."&sensor=false";
	}

	// print message to page and exit
	private function showError($msg)
	{
		echo "<strong>".$msg."</strong>";
		die();
	}

	// helper function to inspect data
	private function pre($array)
	{
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}

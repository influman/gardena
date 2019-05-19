<?php
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";      
	//***********************************************************************************************************************
	// V1.01 : Gardena / copyright Influman 2019
	/**
	* http://www.dxsdata.com/2016/07/php-class-for-gardena-smart-system-api/
	* Ref. http://www.roboter-forum.com/showthread.php?16777-Gardena-Smart-System-Analyse
	*/
	$action = getArg("action", true); 
	$server = getArg("server", true); // VAR1
	$value = getArg("value", false);
	$appareil = getArg("device", false);
	$timer = getArg("timer", false);
	$periph_id = getArg('eedomus_controller_module_id'); 
	if ($action == '' ) {
		die();
	}
	$xml .= "<GARDENA>";
	
	// SESSIONS // Ouverture de sessions
	if ($action != "polling") {
		$tab_param = explode(",",$server);
		$login = $tab_param[0];
		$pass = utf8_decode($tab_param[1]);
		$credentials = '{"sessions":{"email":"'.$login.'","password":"'.$pass.'"}}';                     
		$headers_login = array(                                                                          
            'Content-Type:application/json',                                                                                
            'Content-Length: ' . strlen($credentials));  
		$url = "https://smart.gardena.com/sg-1/sessions";
		$response = httpQuery($url, 'POST', $credentials, NULL, $headers_login, true, false);
		$return_api = sdk_json_decode($response);
		if (array_key_exists("sessions",$return_api)) {
			$token = $return_api['sessions']['token'];
			$user_id = $return_api['sessions']['user_id'];
			$headers = array(                                                                          
            'Content-Type:application/json',                                                                                
            'X-Session:'. $token);  
		} else {
			$xml .= "<STATUS>Unauthorized access, check login and password</STATUS>";
			$xml .= "</GARDENA>";
			sdk_header('text/xml');
			echo $xml;
			die();
		}
	}
    
	// GLOBAL STATUS // Lecture des données Cloud Gardena
	if ($action == "getstatus") { 
		$status = "";
		$tab_devices = array();
		$url = 	"https://smart.gardena.com/sg-1/locations/?user_id=".$user_id;
		$response = httpQuery($url, 'GET', NULL, NULL, $headers, true, false);
		$return_api = sdk_json_decode($response);
		$i = 0;
		foreach($return_api['locations'] as $location) {
			$i++;
			$xml .= "<LOCATION_".$i.">";
			$location_id = $location['id'];
			$location_name = $location['name'];
			$city = $location['geo_position']['city'];
			$xml .= "<ID>".$location_id."</ID>";
			$xml .= "<NAME>".$location_name."</NAME>";
			$xml .= "<CITY>".$city."</CITY>";
			// recherche des devices de cette location
			$url = 	"https://smart.gardena.com/sg-1/devices?locationId=".$location_id;
			$response_device = httpQuery($url, 'GET', NULL, NULL, $headers, true, false);
			$return_device = sdk_json_decode($response_device);
			$xml .= "<DEVICES>";
			$j = 0;
			foreach($return_device['devices'] as $device) {
				$j++;
				$xml .= "<DEVICE_".$j.">";
				$device_id = $device['id'];
				$device_name = $device['name'];
				$device_category = $device['category'];
				$device_state = $device['device_state'];
				$xml .= "<ID>".$device_id."</ID>";
				$xml .= "<NAME>".$device_name."</NAME>";
				$xml .= "<CAT>".$device_category."</CAT>";
				$xml .= "<STATE>".$device_state."</STATE>";
				$tab_devices[$device_name]['location_id'] = $location_id;
				$tab_devices[$device_name]['location_name'] = $location_name;
				$tab_devices[$device_name]['id'] = $device_id;
				$tab_devices[$device_name]['category'] = $device_category;
				$tab_devices[$device_name]['state'] = $device_state;
				// Cas Gateway
				if ($device_category == "gateway") {
					foreach ($device['abilities'] as $abilities) {
						if ($abilities['name'] == "gateway") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "ip_address") {
									$ip_address = $property['value'];
									$xml .= "<IP>".$ip_address."</IP>";
									$tab_devices[$device_name]['ip_address'] = $ip_address;
									break;
								}
							}
						}
					}
					$status .= $device_name."(GW ".$device_state.") ";
				}
				if ($device_category == "mower") {
					foreach ($device['abilities'] as $abilities) {
						if ($abilities['name'] == "mower") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "status") {
									$mower_status = $property['value'];
									$xml .= "<MOWER_STATUS>".$ip_address."</MOWER_STATUS>";
									$tab_devices[$device_name]['mower_status'] = $mower_status;
									break;
								}
							}
						}
					}
					$status .= $device_name."(MW ".$device_state.") ";
				}
				// Cas Sensor
				if ($device_category == "sensor") {
					foreach ($device['abilities'] as $abilities) {
						if ($abilities['name'] == "battery") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "level") {
									$battery_level = $property['value'];
									$xml .= "<BATTERY>".$battery_level."</BATTERY>";
									$tab_devices[$device_name]['battery_level'] = $battery_level;
									break;
								}
							}
						}
						if ($abilities['name'] == "humidity") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "humidity") {
									$humidity = $property['value'];
									$xml .= "<HUMIDITY>".$humidity."</HUMIDITY>";
									$tab_devices[$device_name]['humidity'] = $humidity;
									break;
								}
							}
						}
						if ($abilities['name'] == "ambient_temperature") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "temperature") {
									$temperature = $property['value'];
									$xml .= "<TEMPERATURE>".$temperature."</TEMPERATURE>";
									$tab_devices[$device_name]['temperature'] = $temperature;
									break;
								}
							}
						}
						if ($abilities['name'] == "light") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "light") {
									$light = $property['value'];
									$xml .= "<LIGHT>".$light."</LIGHT>";
									$tab_devices[$device_name]['light'] = $light;
									break;
								}
							}
						}
						$status .= $device_name."(SR ".$device_state.") ";
					}
				}
				
				// Cas Water Control
				if ($device_category == "watering_computer") {
					foreach ($device['abilities'] as $abilities) {
						if ($abilities['name'] == "battery") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "level") {
									$battery_level = $property['value'];
									$xml .= "<BATTERY>".$battery_level."</BATTERY>";
									$tab_devices[$device_name]['battery_level'] = $battery_level;
									break;
								}
							}
						}
						if ($abilities['name'] == "outlet") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "valve_open") {
									if ($property['value']) {
										$valve_open = "O";
									} else {
										$valve_open = "C";
									}
									$xml .= "<VALVE_OPEN>".$valve_open."</VALVE_OPEN>";
									$tab_devices[$device_name]['valve'] = $valve_open;
									break;
								}
							}
						}
						if ($abilities['name'] == "ambient_temperature") {
							foreach($abilities['properties'] as $property){
								if ($property['name'] == "temperature") {
									$temperature = $property['value'];
									$xml .= "<TEMPERATURE>".$temperature."</TEMPERATURE>";
									$tab_devices[$device_name]['temperature'] = $temperature;
									break;
								}
							}
						}
					}
					$status .= $device_name."(WC ".$device_state.") ";
				}
				$xml .= "</DEVICE_".$j.">";
			}
			$xml .= "</DEVICES>";
			$xml .= "</LOCATION_".$i.">";
		}
		saveVariable("GARDENA_DEVICES",$tab_devices);
		$xml .= "<STATUS>".$status."</STATUS>";
		$xml .= "</GARDENA>";
		sdk_header('text/xml');
		echo $xml;
		die();
	}
	
	// COMMANDS // Actionner un appareil
	if (($action == "water" || $action == "mower") && $appareil != "") {
		$tab_devices = loadVariable("GARDENA_DEVICES");
		if (array_key_exists($appareil,$tab_devices)) {
			if ($action == "water") {
				if ($value == "stop") {
					$commands = '{"name":"cancel_override"}';     
				}
				if ($value == "start") {
					if ($timer == "" || $timer == 0) {
						$timer = 15;
					}
					$commands = '{"name":"manual_override","parameters":{"duration":'.$timer.'}}';  
				}
				$url = "https://smart.gardena.com/sg-1/devices/".$tab_devices[$appareil]['id']."/abilities/outlet/command?locationId=".$tab_devices[$appareil]['location_id'];
			
			}
			if ($action == "mower") {
				if ($value == "park_timer") {
					$commands = '{"name":"park_until_next_timer"}';     
				}
				if ($value == "park_notice") {
					$commands = '{"name":"park_until_further_notice"}';     
				}
				if ($value == "resume") {
					$commands = '{"name":"start_resume_schedule"}';     
				}
				if ($value == "start") {
					$commands = '{"name":"start_override_timer","parameters":{"duration":1440}}';  
				}
				$url = "https://smart.gardena.com/sg-1/devices/".$tab_devices[$appareil]['id']."/abilities/mower/command?locationId=".$tab_devices[$appareil]['location_id'];
			}
			$headers = array(                                                                          
            'Content-Type:application/json',                                                                                
            'X-Session:'. $token,
			'Content-Length: '.strlen($commands)); 
			$response = httpQuery($url, 'POST', $commands, NULL, $headers, true, false);
		}
		die();
	}
	
	// POLLING // Lecture des données par capteur
	if ($action == "polling") {
		$tab_devices = loadVariable("GARDENA_DEVICES");
		if (array_key_exists($appareil,$tab_devices)) {
			$xml .= "<ID>".$tab_devices[$appareil]['id']."</ID>";
			$xml .= "<CAT>".$tab_devices[$appareil]['category']."</CAT>";
			$xml .= "<LOCATION_ID>".$tab_devices[$appareil]['location_id']."</LOCATION_ID>";
			$xml .= "<STATE>".$tab_devices[$appareil]['state']."</STATE>";
			if (array_key_exists("temperature", $tab_devices[$appareil])) {
				$xml .= "<TEMPERATURE>".$tab_devices[$appareil]['temperature']."</TEMPERATURE>";
			}
			if (array_key_exists("humidity", $tab_devices[$appareil])) {
				$xml .= "<HUMIDITY>".$tab_devices[$appareil]['humidity']."</HUMIDITY>";
			}
			if (array_key_exists("battery_level", $tab_devices[$appareil])) {
				$xml .= "<BATTERY>".$tab_devices[$appareil]['battery_level']."</BATTERY>";
			}
			if (array_key_exists("light", $tab_devices[$appareil])) {
				$xml .= "<LIGHT>".$tab_devices[$appareil]['light']."</LIGHT>";
			}
			if (array_key_exists("valve", $tab_devices[$appareil])) {
				$xml .= "<VALVE>".$tab_devices[$appareil]['valve']."</VALVE>";
			}
			if (array_key_exists("mower_status", $tab_devices[$appareil])) {
				$xml .= "<MOWER_STATUS>".$tab_devices[$appareil]['mower_status']."</MOWER_STATUS>";
			}
		}
		$xml .= "</GARDENA>";
		sdk_header('text/xml');
		echo $xml;
		die();
	}
?>

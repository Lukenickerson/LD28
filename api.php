<?php


	require_once('database.php');

	
	if (isset($_GET["get"])) 
	{
		
		$jsonArray = array();
		$getWhat = $_GET["get"];
		$cityId = ((isset($_GET["cityId"])) ? intval($_GET["cityId"]) : 0);
		$useCache = ((isset($_GET["cache"])) ? true : false);
		
		
		switch ($getWhat) {
			case "plots" :
				$sql = "SELECT * 
					FROM plot 
					WHERE planet_id = 1";
				if (isset($_GET["cityId"])) $sql .= " AND city_id = " . intval($_GET["cityId"]);
				$q = dbQuery($sql);
				while ($r = $q->fetch_assoc()) {
					$jsonArray[$r["plot_id"]] = array(
						"plotId" 		=> intval($r["plot_id"])
						,"planetId" 	=> intval($r["planet_id"])
						,"surfaceX" 	=> intval($r["surface_x"])
						,"surfaceWidth" => intval($r["surface_width"])
						,"cityId" 		=> intval($r["city_id"])
						,"ownerPersonId"	=> intval($r["owner_person_id"])
						,"founderName" 		=> $r["founder_name"]
					);
				}
				break;
				
			case "floors" :
				$q = getFloors($cityId);
				//var_dump($q);exit;
				while ($r = $q->fetch_assoc()) {
					$jsonArray[$r["floor_id"]] = array(
						"floorId" 		=> intval($r["floor_id"])
						,"plotId" 		=> intval($r["plot_id"])
						,"surfaceX"		=> intval($r["surface_x"])
						,"surfaceY"		=> intval($r["surface_y"])
						,"cityId"		=> intval($r["city_id"])
						,"ownerPersonId" => intval($r["owner_person_id"])
						,"floorTypeKey"	=> $r["floor_type_key"]
						,"capacity"		=> intval($r["capacity"])
						,"rent"			=> intval($r["rent"])
						,"wages"		=> intval($r["wages"])
					);
				}
				break;
				
			case "persons":
				$q = getPersons($cityId);
				//var_dump($q);exit;
				while ($r = $q->fetch_assoc()) {
					$jsonArray[$r["person_id"]] = array(
						"personId" 					=> intval($r["person_id"])
						,"currentLocationType" 		=> $r["current_location_type"]
						,"currentLocationId" 		=> intval($r["current_location_id"])
						,"destinationLocationType" 	=> $r["destination_location_type"]
						,"destinationLocationId" 	=> intval($r["destination_location_id"])
						,"action"			=> $r["action"]
						,"firstName" 		=> $r["first_name"]
						,"lastName" 		=> $r["last_name"]
						,"homeFloorId"		=> intval($r["home_floor_id"])
						,"workFloorId" 		=> intval($r["work_floor_id"])
						,"genderKey" 		=> intval($r["gender_key"])
						,"coins"			=> intval($r["coins"])
					);
				}
				break;

			case "cities" :
				$sql = "SELECT * FROM city WHERE planet_id = 1";
				$q = dbQuery($sql);
				while ($r = $q->fetch_assoc()) {
					$cityId = intval($r["city_id"]);
					$jsonArray[$r["city_id"]] = array(
						"cityId" 		=> $cityId
						,"cityName" 	=> $r["city_name"]
						,"population"	=> getCityPopulation($cityId)
					);
				}
				break;				
				
			case "myPerson":
				$playerId = loginPlayer();
				$playerPersonId = getPlayerPersonId($playerId);
				$jsonArray = array(
					"playerId"		=> $playerId
					,"personId"		=> $playerPersonId
				);
				break;
		}
		$json = json_encode($jsonArray);
		if ($useCache) {
			if (isset($_SESSION[$getWhat]) && $json == $_SESSION[$getWhat]) {
				$json = '{"_isChanged":false}';
			} else {
				$_SESSION[$getWhat] = $json;
			}
		}
		echo $json;
	
	} 
	else if (isset($_GET["set"])) 
	{
	
	
	
	
	
	} 
	else if (isset($_GET["claim"])) 
	{
		$plotId = $_GET["claim"];
		$playerId = loginPlayer();
		$playerPersonId = getPlayerPersonId($playerId);
		$isSuccess = setPlotOwner($plotId, $playerPersonId);
		$jsonData = array(
			"_success"	=> $isSuccess
			,"_message" => ($isSuccess ? "Plot claimed" : "Claim failed. Do you have enough coins?")
		);
		echo json_encode($jsonData);
	}
	else if (isset($_GET["build"]) && isset($_GET["type"])) 
	{
		$plotId = $_GET["build"];
		$type = $_GET["type"];
		$playerId = loginPlayer();
		$playerPersonId = getPlayerPersonId($playerId);	
		$isSuccess = addFloor($plotId, $type, $playerPersonId);
		if (gettype($isSuccess) == 'boolean' && $isSuccess) {
			$jsonData = array(
				"_success"	=> $isSuccess
				,"_message" => "Success"
			);
		} else {
			$jsonData = array(
				"success"	=> false
				,"_message" => $isSuccess
			);		
		}
		echo json_encode($jsonData);

	}
	else if (isset($_GET["make"])) 
	{
		$jsonData = array();
		switch ($_GET["make"]) {
			case "person":
				$isSuccess = makeNewPerson();
				$jsonData = array(
					"success"	=> $isSuccess
				);
				break;
				
		
		}
		echo json_encode($jsonData);
		
	} else {
	
		$jsonData = array(
			"success"	=> false
		);
		echo json_encode($jsonData);
	}


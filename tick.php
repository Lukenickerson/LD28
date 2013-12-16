<?php

	require_once('database.php');
	
	$jsonArray = array();
	
	$qLock = dbQuery("LOCK TABLES planet WRITE");
	$sql = "SELECT tick, last_tick_datetime FROM planet WHERE planet_id = 1";
	$q = dbQuery($sql);
	$planetRow = $q->fetch_assoc();
	$lastTickTime  = strtotime($planetRow["last_tick_datetime"]);
	$now = time();
	$diff = $now - $lastTickTime;
	$tick = intval($planetRow["tick"]);
	$scheduleIndex = $tick % 24; // 0 - 23
	$jsonArray["gameHour"] = ($scheduleIndex + 1);	
	
	if ($diff < 4) {
		$qLock = dbQuery("UNLOCK TABLES");
		$jsonArray["_message"] =  "Not enough time has passed";
		
	} else {
		$tick += 1;
		$sql = "UPDATE planet 
			SET tick = " . $tick . ", last_tick_datetime = '". date("Y-m-d H:i:s", $now) . "'
			WHERE planet_id = 1";
		$q = dbQuery($sql);
		$qLock = dbQuery("UNLOCK TABLES");
	
		$jsonArray["_message"] =  "Running Tick Process";
		// One tick = 4 seconds RL = 1 hour
		// 8 seconds RL = 2 hours
		// 32 seconds RL = 8 hours = work day
		// 96 seconds RL = 24 hours
		// 672 seconds RL (~11 mins) = 1 week

		// What time of day is it?
		// 8 hours of work, 8 hours of sleep, 8 hours free
		$scheduleArray = array(
			"sleep"		// 1:00 AM
			,"sleep"	// 2
			,"sleep"	// 3
			,"sleep"	// 4
			,"sleep"	// 5
			,"sleep"	// 6
			,"sleep"	// 7
			,"eat"		// 8:00 AM
			,"gotowork"	// 9:00 AM
			,"work"		// 10
			,"work"		// 11
			,"work"		// 12 noon
			,"work"		// 1:00 PM (13)
			,"work"		// 2 (14)
			,"work"		// 3 (15)
			,"work"		// 4 (16)
			,"work"		// 5 (17)
			,"gotohome" // 6 (18)   // *** change to gotofood when there are restaraunts
			,"eat"		// 7 (19)
			,"free"		// 8 (20)
			,"free"		// 9 (21)
			,"free"		// 10 (22)
			,"gotohome"	// 11 (23)
			,"sleep"	// 12:00 AM (24) midnight
		);
		$isRentDue = ($scheduleIndex == 0) ? true : false;
		
		//$qFloors = getFloors();
		$floorCapacityR = getFloorCapacities("R");
		$floorCapacityC = getFloorCapacities("C");
		//var_dump($floorCapacityR);var_dump($floorCapacityC);exit;

		//$qRFloors = getFloorsByType("R");
		//$qCFloors = getFloorsByType("C");
		$qPersons = getNPCs();
		$countOfHomeless = 0;
		$countOfUnemployed = 0;
		while ($person = $qPersons->fetch_assoc()) {
		
			// If traveling, then arrive at destination
			if ($person["action"] == "goto" && $person["destination_location_id"] > 0) {
				// *** account for different travel distances
				// Currently you can travel anywhere in one tick
				$person["current_location_type"] = $person["destination_location_type"];
				$person["current_location_id"] = $person["destination_location_id"];
			}
			$person["destination_location_type"] = '';
			$person["destination_location_id"] = 0;
			
			// Get starting money
			$person["coins"] = intval($person["coins"]);
		
			// Get scheduled action for this person
			$scheduleIndex += intval($person["schedule_offset"]);
			if ($scheduleIndex < 0) {		$scheduleIndex += 24; }
			else if ($scheduleIndex > 23) {	$scheduleIndex -= 24; }
			$action = $scheduleArray[$scheduleIndex];
		
			// Checks...
			if ($person["home_floor_id"] == 0) { // Homeless
				
				//$action = "wait";
				// Look for a house that has capacity left
				foreach ($floorCapacityR as $floorId => $capacityLeft) {
					if ($capacityLeft > 0) {
						$floorCapacityR[$floorId] = ($capacityLeft - 1);
						$person["home_floor_id"] = $floorId;
					}
				}
				// Still homeless?
				if ($person["home_floor_id"] == 0) $countOfHomeless++;
				
			} else { // Person has a home
				// Is person in the right spot for their action?
				if ($action == "sleep" || $action == "eat") {
					if ($person["current_location_type"] == "floor"
						&& $person["current_location_id"] == $person["home_floor_id"]) {
						// Eating or sleeping... nothing special to do
					} else {
						$action = "gotohome";
					}
				}
				if ($action == "gotohome") {
					$action = "goto";
					$person["destination_location_type"] = 'floor';
					$person["destination_location_id"] = $person["home_floor_id"];
				}
				
				if ($isRentDue) {
				
					// *** use the real rent value
					
					$rent = 50;
					$person["coins"] -= $rent;
					
					// Make rent go into the owner's pocket
					$homeFloorRecord = getFloor($person["home_floor_id"]);
					$ownerPersonId = $homeFloorRecord["owner_person_id"];
					$ownerPerson = getPerson($ownerPersonId);
					setPersonCoins($ownerPersonId, ($ownerPerson["coins"] + $rent));
					
					
					if ($person["coins"] <= 0) { // Eviction!
						$person["home_floor_id"] = null;
					}
				}
				
			}
			
			if ($person["work_floor_id"] == 0) { // Unemployed
				
				//$action = "wait";
				// Look for a workplace that has capacity left
				foreach ($floorCapacityC as $floorId => $capacityLeft) {
					if ($capacityLeft > 0) {
						$floorCapacityC[$floorId] = ($capacityLeft - 1);
						$person["work_floor_id"] = $floorId;
					}
				}
				// Still jobless?
				if ($person["work_floor_id"] == 0) $countOfUnemployed++;
				
			} else { // Person has a job
				// Is person in the right spot for their action?
				if ($action == "work") {
					if ($person["current_location_type"] == "floor"
						&& $person["current_location_id"] == $person["work_floor_id"]) {
					
						// *** make this actually use the wages value
						// *** make this come out of the owner's pocket?
						$person["coins"] += 10;
						
					} else {
						$action = "gotowork";
					}
				}
				if ($action == "gotowork") {
					$action = "goto";
					$person["destination_location_type"] = 'floor';
					$person["destination_location_id"] = $person["work_floor_id"];
				}
			}
			
			if ($action == "free") {
			
				// *** Do wandering
				
			}
			
	
			$person["action"] = $action;
			$q = savePerson($person);
			
		}

		if ($countOfUnemployed == 0 && $countOfHomeless == 0) {
			makeNewPerson();
		}

		// *** Every certain number of ticks (every few game days)...
			// *** Compare current job to other jobs
		
		// *** Every week...
			// *** Look for new nomination
	
	
	}
	
	$jsonArray["_diff"] = $diff;
	$jsonArray["_tick"] = $tick;
	$jsonArray["_runTime"] = (time() - $now);
	$json = json_encode($jsonArray);
	echo $json;
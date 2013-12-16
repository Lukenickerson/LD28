<?php

	session_start();
	require_once('config.php');
	
	global $GAME;
	$GAME = array(
		"plotCost" => 500
		,"buildCost" => 100
	);

	function connectToDatabase ($dbSettings) 
	{
		//shell_exec("mysqladmin flush-hosts");
		$mysqli = new mysqli(
			$dbSettings["hostname"]
			,$dbSettings["username"]
			,$dbSettings["password"]
			,$dbSettings["databaseName"]
		);
	
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL Database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		return $mysqli;
	}

	global $db;
	$db = connectToDatabase($dbSettings);
	
	function dbQuery ($sql) 
	{
		global $db;

		// Less secure, but simpler, way to do queries
		$result = $db->query($sql);

		if (!$result) {
			echo "Query failed: (" . $db->errno . ") " . $db->error;
		}
		return $result;
	}
	
	//==================================== LOGIN PLAYER ====================
	
	function makeNewPlayer($ip) {
		//$name = getRandomName();
		$qInsertPlayer = dbQuery(
			"INSERT INTO player (ip_address, last_login_datetime) "
			. " VALUES ('".$ip."', '".date(DATE_ISO8601)."')");
		return $qInsertPlayer;	
	}
	
	function loginPlayer($force = false) 
	{
		if (isset($_SESSION['playerId'])) {
			return $_SESSION['playerId'];
		} else {
			$ip = $_SERVER["REMOTE_ADDR"];
			$sql = "SELECT * FROM player WHERE ip_address = '" . $ip . "'";
			$qPlayer = dbQuery($sql);
			
			if (!$qPlayer || is_null($qPlayer->num_rows) || $qPlayer->num_rows == 0) {
				$qInsertPlayer = makeNewPlayer($ip);
				//var_dump($qInsertPlayer);
				$qPlayer = dbQuery($sql);
			}
			// *** set last login datetime
			$playerRow = $qPlayer->fetch_assoc();
			$playerId = intval($playerRow["player_id"]);
			$_SESSION['playerId'] = $playerId;
			return $playerId;
		}
	}
	
	//========================== GETS ==========================================
	
	function getPlayerPersonId($playerId = 0) {
		if ($playerId > 0) {
			$sql = "SELECT * FROM person WHERE player_id = " . intval($playerId);
			$qPerson = dbQuery($sql);
			if (!$qPerson || is_null($qPerson->num_rows) || $qPerson->num_rows == 0) {
				makeNewPerson($playerId);
				$qPerson = dbQuery($sql);
			}
			$personRow = $qPerson->fetch_assoc();
			return intval($personRow["person_id"]);
		} else {
			return 0;
		}
	}
	
	function getPlot($plotId=0) {
		$sql = "SELECT * FROM plot WHERE plot_id = " . intval($plotId);
		$q = dbQuery($sql);
		if (is_null($q->num_rows) || $q->num_rows == 0) {
			return false;
		} else {
			$row = $q->fetch_assoc();
			return $row;
		}
	}	
	
	function getPerson($personId=0) {
		$sql = "SELECT * FROM person WHERE person_id = " . intval($personId);
		$qPerson = dbQuery($sql);
		if (is_null($qPerson->num_rows) || $qPerson->num_rows == 0) {
			return false;
		} else {
			$personRow = $qPerson->fetch_assoc();
			return $personRow;
		}
	}
	
	function getPersons($cityId=0){
		$sql = "SELECT p.* 
			FROM person p";
			/*
			INNER JOIN (
				SELECT *
				FROM plot
				WHERE planet_id = 1";
		if ($cityId > 0) { 
			$sql .= " AND city_id = " . intval($cityId);
		}
		$sql .= "
			) x
			ON x.plot_id = p.current_plot_id";
			*/
		$q = dbQuery($sql);
		return $q;
	}
	
	function getNPCs(){
		$sql = "SELECT p.* 
			FROM person p
			WHERE p.player_id is null";
			// *** Should add something like this back
			/*
			INNER JOIN (
				SELECT *
				FROM plot
				WHERE planet_id = 1
			) x
			ON x.plot_id = p.current_plot_id
			WHERE p.player_id is null";
			*/
		$q = dbQuery($sql);
		return $q;
	}	
	
	
	function getFloor($floorId=0){
		$sql = "SELECT f.*, x.surface_x, x.owner_person_id, x.city_id
			FROM floor f
			INNER JOIN (
				SELECT plot_id, surface_x, owner_person_id, city_id
				FROM plot
				WHERE planet_id = 1
			) x
			ON x.plot_id = f.plot_id
			WHERE f.floor_id = " . intval($floorId);
		$q = dbQuery($sql);
		if (is_null($q->num_rows) || $q->num_rows == 0) {
			return false;
		} else {
			$r = $q->fetch_assoc();
			return $r;
		}
	}		
	
	function getFloors($cityId=0){
		$sql = "SELECT f.*, x.surface_x, x.owner_person_id, x.city_id
			FROM floor f
			INNER JOIN (
				SELECT plot_id, surface_x, owner_person_id, city_id
				FROM plot
				WHERE planet_id = 1";
		if ($cityId > 0) {  
			$sql .= " AND city_id = " . intval($cityId);
		}
		$sql .= "
			) x
			ON x.plot_id = f.plot_id";
		$q = dbQuery($sql);
		return $q;
	}
	
	function getFloorsByType($type="R"){
		$type = strtoupper(substr($type, 0, 1));
		if ($type == "R" || $type == "C" || $type == "I" || $type == "F") {
			$sql = "SELECT f.*, x.surface_x, x.owner_person_id, x.city_id
				FROM floor f
				INNER JOIN (
					SELECT plot_id, surface_x, owner_person_id, city_id
					FROM plot
					WHERE planet_id = 1
				) x
				ON x.plot_id = f.plot_id
				WHERE f.floor_type_key = '" . $type . "'";
			$q = dbQuery($sql);
			return $q;
		} else {
			return false;
		}
	}	
	
	function getFloorsByPlotId($plotId=0) {
		$sql = "SELECT * FROM floor WHERE plot_id = " . intval($plotId);
		$q = dbQuery($sql);
		if (is_null($q->num_rows) || $q->num_rows == 0) {
			return false;
		} else {
			return $q;
		}		
	}
	
	function getFloorCapacities($type="R"){
		$colName = ($type == "R") ? "home_floor_id" : "work_floor_id";
		
		$sql = "SELECT f.*, x.person_count
			FROM floor f
			LEFT JOIN (
				SELECT " . $colName . " AS floor_id, COUNT(person_id) AS person_count
				FROM person
				GROUP BY " . $colName . "
			) x
			ON x.floor_id = f.floor_id
			WHERE f.floor_type_key = '" . $type . "'";
		$q = dbQuery($sql);
		$floorArray = array();
		while ($f = $q->fetch_assoc()) {
			$floorArray[$f["floor_id"]] = intval($f["capacity"]) - intval($f["person_count"]);
		}
		return $floorArray;
	}
	
	function getMaxSurfaceY($plotId=0){
		$sql = "SELECT MAX(surface_y) AS max_surface_y 
			FROM floor 
			WHERE plot_id = " . intval($plotId);
		$q = dbQuery($sql);
		if (is_null($q->num_rows) || $q->num_rows == 0) {
			return -1;
		} else {
			$floorRow = $q->fetch_assoc();
			if (is_null($floorRow["max_surface_y"])) {
				return -1;
			} else {
				return $floorRow["max_surface_y"];
			}
		}
	}
	
	function getCityPopulation($cityId=0){
		$cityId = intval($cityId);
		$pop = 0;
		$sql = "SELECT COUNT(p.person_id) AS person_count
			FROM person p
			INNER JOIN (
				SELECT plot_id
				FROM plot
				WHERE city_id = " . $cityId . "
			) x
			ON x.plot_id = p.current_location_id
			WHERE p.current_location_type = 'plot'";
		$q = dbQuery($sql);
		$row = $q->fetch_assoc();
		$pop += intval($row["person_count"]);
		
		$sql = "SELECT COUNT(p.person_id) AS person_count
			FROM person p, plot pl, floor f
			WHERE p.current_location_type = 'floor'
				AND p.current_location_id = f.floor_id
				AND f.plot_id = pl.plot_id
				AND pl.city_id = " . $cityId;
		$q = dbQuery($sql);
		$row = $q->fetch_assoc();		
		$pop += intval($row["person_count"]);

		return $pop;
	}

//====================================== EDITS - INSERTS, UPDATES ================	
	
	
	function makeNewPerson($playerId=0) 
	{
		$randomPlotId = rand(1,299); // *** fix...
		// *** Get array of plot ids
		// *** pick a random index from the array to get the plot id
		
		$genderKey = ((rand(1,2) == 1) ? "M" : "F");
		$sql = "INSERT INTO person (first_name, last_name "
			. (($playerId > 0) ? ",player_id" : "")
			. ",current_location_type,current_location_id,gender_key,coins) VALUES ("
			. "'" . getRandomName("FIRST", $genderKey) . "'"
			. ",'" . getRandomName("LAST", $genderKey) . "'"
			. (($playerId > 0) ? "," . $playerId : "")
			. ",'plot'"
			. "," . $randomPlotId 
			. ",'" . $genderKey . "'"
			
			. "," . (($playerId > 0) ? "1100" : "60")
			. ")";
		$qPerson = dbQuery($sql);
		return $qPerson;
	}
	
	function savePerson($data) {
		$sql = "UPDATE person SET "
			. " home_floor_id = " . intval($data["home_floor_id"])
			. ",work_floor_id = " . intval($data["work_floor_id"])
			. ",current_location_type = '" . $data["current_location_type"] . "'"
			. ",current_location_id = " . intval($data["current_location_id"])
			. ",action = '" . $data["action"] . "'"
			. ",destination_location_type = '" . $data["destination_location_type"] . "'"
			. ",destination_location_id = " . intval($data["destination_location_id"])
			. ",coins = " . intval($data["coins"])
			. " WHERE person_id = " . intval($data["person_id"]);
		$q = dbQuery($sql);
		return $q;
	}

	
	function setPersonCoins($personId=0, $coins=0) {
		$sql = "UPDATE person SET coins = " . intval($coins) . " WHERE person_id = " . intval($personId);
		$q = dbQuery($sql);
	}
	
	function makeVagabonds(){
		$sql = "UPDATE person SET home_floor_id = 0, work_floor_id = 0";
		$q = dbQuery($sql);
	}
	
	
	function setPlotOwner($plotId, $personId) 
	{
		global $GAME;
		$personRow = getPerson($personId);
		$personsCoins = intval($personRow["coins"]);
		//var_dump($personsCoins);
		//var_dump($GAME["plotCost"]);
		if ($personsCoins >= $GAME["plotCost"]) {
			setPersonCoins($personId, ($personsCoins - $GAME["plotCost"]));
			$sql = "UPDATE plot SET owner_person_id = " . intval($personId) 
				. " WHERE plot_id = " . intval($plotId);
			$q = dbQuery($sql);
			return true;
		} else {
			return false;
		}
	}
	
	function addFloor($plotId, $type, $personId)
	{
		global $GAME;
		$personRow = getPerson($personId);
		$personsCoins = intval($personRow["coins"]);
		// Make sure this person owns the plot...
		$plotRow = getPlot($plotId);
		$type = strtoupper(substr($type, 0, 1));
		if ($type == "R" || $type == "C" || $type == "I" || $type == "F") {
			if ($plotRow["owner_person_id"] == $personId) {
				// Get all floors on plot
				//$qFloors = getFloorsByPlotId($plotId);
				
				$surfaceY = getMaxSurfaceY($plotId) + 1;
				if ($surfaceY > 4) {
					return "Too high!";
				} else {
			
					if ($personsCoins > $GAME["buildCost"]) {
						setPersonCoins($personId, ($personsCoins - $GAME["buildCost"]));
						$sql = "INSERT INTO floor (plot_id, surface_y, floor_type_key, capacity, rent, wages)"
							. " VALUES ("
							. $plotId
							. "," . $surfaceY
							. ", '" . $type . "'"
							. ",1"
							. "," . (($type == "R") ? 10 : 0)
							. "," . (($type != "R") ? 10 : 0)
							. ")";
						$q = dbQuery($sql);
						return true;
					} else { // Not enough cash
						return "Not enough coins";
					}
				}
			} else { // Plot not owned by person
				return "Plot not owned by you";
			}
		} else {	// Bad type
			return "Bad type";
		}
	}
	
	
	
	//==============================
	
	
	function getRandomName($type="FIRST", $genderKey="F") {
		if ($type == "FIRST") {
			if ($genderKey == "F") {
				$nameArray = array("Chrissy", "Susan", "Nina", "Jane", "Eve", "Sara", "Fatiha"
					,"Meriem", "Karima", "Nadia", "Isabel", "Rania", "Mariam", "Shayma"
					,"Zoe", "Sofia", "Mia", "Valentina", "Maria", "Alma", "Isabella", "Bella"
					,"Catalina", "Olivia", "Mary", "Margaret", "Brooklyn", "Emma", "Lea"
					,"Regina", "Salome", "Lisa", "Ella", "Chole", "Lily", "Hannah", "Abigail"
					,"Elizabeth", "Beth", "Patricia", "Dorothy", "Victoria", "Nicole"
					,"Samantha", "Jennifer", "Barbara", "Madison", "Jada", "Nevaeh", "Kayla"
					,"Camila", "Ava", "Emily", "Melissa", "Britney", "Candice", "Joyananda"
					,"Katie", "Joan", "Diane", "Shannon", "Kelly", "Christina", "Sondra"
					,"Jessica", "Tara", "Miranda", "Ruth", "Kim", "Alicia", "Megan"
					,"Ana", "Melanie", "Amanda", "Rachel", "Monique", "Leanne", "Rebecca"
					,"Raquel", "Lu", "Kerri", "Robyn", "Suzanne", "Frances", "Kacey"
					,"Rose", "Kerry", "Lori", "Naomi", "Kirby", "Kristi", "Ashley"
					,"Caroline", "Rosa", "Vikky", "Tessa", "Simone", "Dora", "Darylanne"
					,"Claire", "Alison", "Jacqueline", "Jaclyn", "Malini", "Jess", "Valerie"
					,"Marilyn", "Erin", "Tracy", "Mehnaz", "Sung", "Karen", "Stephanie"
					,"Jodi", "Ariel", "Dona", "Amy", "Abbie", "Michelle", "Sivan", "Mollie"
					,"Jana", "Maryam", "Zahra", "Yarin", "Zara", "Aya", "Ying", "Yan", "Li"
					,"Xiaoyan", "Lili", "Angel", "Pari", "Diya", "Hillary", "Gem"
					
				);
			} else {
				$nameArray = array("Luke", "Carl", "Elijah", "John", "Adam", "Moe", "Lucius"
					,"Omar", "Mohammed", "Brahim", "Ahmed", "Ali", "Karim", "Youssef"
					,"Hassan", "Juan", "Benjamin", "Santiago", "Lucas", "Joaquin", "Santino"
					,"Ian", "Mateo", "Daniel", "Dylan", "Miguel", "Arthur", "Jacob", "Liam"
					,"Carter", "Ethan", "William", "Jeffery", "Botch", "Martin", "Bob", "Jim"
					,"James", "Nicolas", "Raphael", "Mason", "Jack", "Logan", "Hudson", "Samuel"
					,"Diego", "Jason", "Mark", "Matthew", "Luke", "Kevin", "Tyler", "Kirk"
					,"Spock", "Alex", "Naimur", "Joseph", "David", "Robert", "Michael", "Chuck"
					,"Charles", "Jonathan", "Colin", "Elric", "Frank", "Justin", "Jesse"
					,"Chris", "Brian", "Brendan", "Nikhil", "Philip", "Henry", "Patrick"
					,"George", "Kent", "Steve", "Jose", "Timothy", "Giovanni", "Ash", "Ryan"
					,"Raymond", "Dan", "Thomas", "Theo", "Tapan", "Mike", "Kris", "Rodney"
					,"Evan", "Paul", "John", "Tom", "Jai", "Blaise", "Ivan", "Craig", "Adrian"
					,"Sean", "Justin", "Eric", "Scott", "Derek", "Anthony", "Wahed", "Abdullah"
					,"Amir", "Yosef", "Mahmed", "Julian", "Jude", "Khalid", "Omar", "Ahmet"
					,"Wei", "Jie", "Hao", "Feng", "Noel", "Leon", "Otto", "Notch", "Connor"
					,"Peter", "Quincy", "Ulysses", "Rutherford", "Chester", "Grover", "Calvin"
					,"Barack", "Richard", "Ronald"
				);
			}
		} else {
			$nameArray = array("Doe", "Eden", "Elk", "Moose", "Eagle", "Durden", "Picard"
				,"Shatner", "Ahmed", "Rahman", "Wang", "Smith", "Johnson", "Williams", "Brown"
				,"Jones", "Miller", "Davis", "Garcia", "Rogriquez", "Wilson", "Martinez"
				,"Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin", "Jackson"
				,"Thompson", "White", "Lopez", "Lee", "Harris", "Clark", "Lewis", "Robinson"
				,"Walker", "Perez", "Hall", "Young", "Allen", "Sanchez", "Wright", "King"
				,"Green", "Baker", "Adams", "Nelson", "Hill", "Ramirez", "Campbell", "Mitchell"
				,"Roberts", "Carter", "Philips", "Evans", "Turner", "Parker", "Collins", "Edwards"
				,"Stewart", "Flores", "Morris", "Nguyen", "Murphy", "Rivera", "Cook", "Rogers"
				,"Morgan", "Peterson", "Cooper", "Reed", "Bailey", "Bell", "Gomez", "Howard"
				,"Cruz", "Price", "Hughes", "Long", "Ross", "Foster", "Perry", "Ortiz", "Butler"
				,"Barnes", "Fisher", "Muller", "Schmidt", "Fischer", "Meyer", "Weber", "Schulz"
				,"Wagner", "Becker", "Hoffmann", "Papadopoulos", "Walsh", "Byrne", "Sullivan"
				,"Kennedy", "Doyle", "Rossi", "Ferrari", "Marino", "Romano", "Molyneux", "Silva"
				,"Smirnov", "Ivanov", "Popov", "Kuzetsov", "Sokolov", "Volkov", "Kaya", "Wood"
				,"Robinson", "Washington", "Adams", "Jefferson", "Monroe", "Madison", "VanBuren"
				,"Harrison", "Edison", "Ford", "Tyler", "Polk", "Fillmore", "Pierce", "Buchanan"
				,"Lincoln", "Grant", "Cleveland", "York", "Garfield", "Hayes", "Taft", "Wilson"
				,"Harding", "Hoover", "Truman", "Eisenhower", "Reagan", "Clinton", "Bush", "Obama"
				,"Cheney"
			);
		}
		$nameIndex = rand(1, count($nameArray)) - 1;
		return $nameArray[$nameIndex];
	}
	
	
	
	
	
	
	
	
	/*
	for ($i = 100; $i < 200; $i++) {
		echo $i;
		dbQuery("
			INSERT INTO plot (
				planet_id, surface_x, surface_width, city_id)
			VALUES (1, " . $i . ", 2, 3)");
	}
	dbQuery("UPDATE plot SET city_id = 2 WHERE surface_x >= 100 AND surface_x < 200");
	*/
	
	
	/*
	function dbPreparedQuery ($sql, $args) 
	{
		global $db;
		
		$statement = $db->prepare($sql);
		call_user_func_array(array($statement, "bind_param"), $args);
		$statement->execute();
		$result = $statement->get_result();
		
		if (!$result) {
			echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}		
		return $result;
	}
	*/



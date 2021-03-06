<?php
	/*
	 * Copyright 2014 Lexteam (https://github.com/Lexteam)
	 * 
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 * 
	 *   http://www.apache.org/licenses/LICENSE-2.0
	 * 
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	*/

	//setup database
	include('constants.php');
	mysql_connect($host, $user, $password) or die(mysql_error());
	mysql_select_db($database) or die(mysql_error());
	checkTable('pack');
	checkTable('stats');
	checkTable('packdata');
	
	//Set header
	header('Content-type: application/json');
	
	function checkTable($tablename) { // Used for testing purposes, will probably not be in final version.
		if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE '". $tablename. "'")) == 1) {
			if($tablename == "pack") {
				mysql_query("CREATE TABLE ". $tablename. "(
					id INT NOT NULL AUTO_INCREMENT, 
					PRIMARY KEY(id), 
					position INT NOT NULL, 
					name VARCHAR(30) NOT NULL, 
					safeName VARCHAR(30) NOT NULL, 
					type VARCHAR(10) NOT NULL, 
					createServer TINYINT(1) NOT NULL, 
					leaderboards TINYINT(1) NOT NULL, 
					logging TINYINT(1) NOT NULL,
					crashReports TINYINT(1) NOT NULL, 
					description VARCHAR(500) NOT NULL, 
					supportURL VARCHAR(250) NOT NULL, 
					websiteURL VARCHAR(250) NOT NULL)")
				or die(mysql_error()); 
			} elseif($tablename == "stats") {
				mysql_query("CREATE TABLE ". $tablename. "(
					option_id INT NOT NULL AUTO_INCREMENT, 
					PRIMARY KEY(option_id),
					option_name VARCHAR(25) NOT NULL, 
					option_value INT NOT NULL)")
				or die(mysql_error()); 
				$sql1 = "INSERT INTO `packs`.`stats` (`option_id`, `option_name`, `option_value`) VALUES (NULL, 'exe', '0');";
				$sql2 = "INSERT INTO `packs`.`stats` (`option_id`, `option_name`, `option_value`) VALUES (NULL, 'zip', '0');";
				$sql3 = "INSERT INTO `packs`.`stats` (`option_id`, `option_name`, `option_value`) VALUES (NULL, 'jar', '0');";
				mysql_query($sql1) or die(mysql_error());
				mysql_query($sql2) or die(mysql_error());
				mysql_query($sql3) or die(mysql_error());
			} else {
				mysql_query("CREATE TABLE ". $tablename. "(
					version VARCHAR(15) NOT NULL, 
					minecraft VARCHAR(10) NOT NULL, 
					published INT NOT NULL,
					__LINK VARCHAR(250) NOT NULL,
					changelog VARCHAR(500) NOT NULL, 
					recommended TINYINT(1) NOT NULL,
					hasJson TINYINT(1) NOT NULL,
					canUpdate TINYINT(1) NOT NULL,
					isDev TINYINT(1) NOT NULL,
					installed INT NOT NULL)")
				or die(mysql_error()); 
			}
		}
	}
	
	function getPageURL() {
		include('constants.php');
		$pageURL = $_SERVER["REQUEST_URI"];
		$url = str_replace($url_cutoff, "", $pageURL);
		return $url;
	}
	
	//functions lists
	$whitelist_json = file_get_contents('versions/functions.json');
	$whitelist = json_decode($whitelist_json, true);
	$api_versions_json = file_get_contents('versions/versions.json');
	$api_versions = json_decode($api_versions_json, true)['versions'];
	
	//call the passed in function
	$method = explode("/", getPageURL());
	$api_version = $method[0];
	if(in_array($api_version, $api_versions)) {
		include("versions/". $api_version. '.php');
	}
	if(count($method) > 1) {
		$command = $method[1];
	} else {
		$command = null;
	}
	$arguments = $method;
	
	if(!empty($whitelist[$api_version])) {
		$functionlist = $whitelist[$api_version];
	} else {
		$functionlist = array();
	}
	if(!$api_version == null && in_array($command, $functionlist)) {
		if($command == "pack") {
			printResponse(getPack($arguments, $api_version, $api_versions));
		} else {
			printResponse($command($arguments, $api_version, $api_versions));
		}
	} else {
		printResponse(error($api_version, $api_versions));
	}
	
	//methods
	function getResponse($error, $code, $message, $array) {
		if($error == null && !$error == 0) {
			if($array == null) {
				$error = true;
			} else {
				$error = false;
			}
		}
		$response = array(
			"error" => $error,
			"code" => $code,
			"message" => $message,
			"data" => $array
		);
		return json_encode($response);
	}
	function error($api_version, $api_versions) {
		$message = "API Call Path Not Found";
		$code = 404;
		if(!in_array($api_version, $api_versions)) {
			$message = "API Version Not Specified";
			$code = 402;
		}
		return getResponse(true, $code, $message, null);
	}
	function printResponse($response) {
		exit($response);
	}
?>
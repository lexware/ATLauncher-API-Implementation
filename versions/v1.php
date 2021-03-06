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
	
	// methods
	function getPack($arguments, $api_version, $api_versions) { // actually pack, but since there is a function in php called pack
		if(count($arguments) == 3) { // /v1/pack/name
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$packs[] = $pack;
				if($pack['safeName'] == $arguments[2]) {
					$pack_array = $pack;
				}
			}
			if($pack_array == null) {
				return error($api_version, $api_versions);
			}
				
			checkTable($pack_array['safeName']. 'Version');
			$versions = array();
			$version_sql = mysql_query("select * from ". $pack_array['safeName']. "Version");
			while($version = mysql_fetch_array($version_sql)) {
				$versionsResponce = array(
					'version' => $version['version'],
					'minecraft' => $version['minecraft'],
					'published' => $version['published'],
					'__LINK' => $version['__LINK']
				);
				$versions[] = $versionsResponce;
			}
				
			$response = array(
				'id' => $pack_array['id'],
				'name' => $pack_array['name'],
				'safeName' => $pack_array['safeName'],
				'type' => $pack_array['type'],
				'versions' => array_reverse($versions),
				'description' => $pack_array['description'],
				'supportURL' => $pack_array['supportURL'],
				'websiteURL' => $pack_array['websiteURL']
			);
				
			return getResponce(false, 200, null, $response);
		} elseif(count($arguments) == 4) { // /v1/pack/name/version
			checkTable($arguments[2]. 'Version');
			$version_response = null;
			$version_sql = mysql_query("select * from ". $arguments[2]. "Version");
			while($version = mysql_fetch_array($version_sql)) {
				$recommended = true;
				if(!$version['recommended'] == 1) {
					$recommended = false;
				}
				$versionsResponse = array(
					'version' => $version['version'],
					'minecraftVersion' => $version['minecraft'],
					'published' => $version['published'],
					'changelog' => $version['changelog'],
					'recommended' => $recommended
				);
				if($version['version'] == $arguments[3]) {
					$version_response[] = $versionsResponse;
				}
			}
			if($version_response == null) {
				return error($api_version, $api_versions);
			}
				
			return getResponse(false, 200, null, $version_response);
		} elseif($arguments[3] == 'installed') {
			if(count($arguments) == 6) {
				// time out and +1 to installed variable
			} else {
				return getResponse(true, 404, 'Version Not Found!', null);
			}
		} else {
			return error($api_version, $api_versions);
		}
	}
	function packs($arguments, $api_version, $api_versions) {
		if($arguments[2] == "simple") { // /v1/packs/simple
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$packResponse = array(
					'id' => $pack['id'],
					'name' => $pack['name'],
					'safeName' => $pack['safeName'],
					'type' => $pack['type'],
					'__LINK' => $pack['websiteURL']
				);
				$packs[] = $packResponse;
			}
			return getResponse(false, 200, null, $packs);
		} elseif($arguments[2] == "full" && $arguments[3] == "all") { // /v1/packs/full/all
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$version_sql = mysql_query("select * from ". $pack['safeName']. "Version");
				$version_response = array();
				while($version = mysql_fetch_array($version_sql)) {
					$versionsResponse = array(
						'version' => $version['version'],
						'minecraft' => $version['minecraft'],
						'published' => $version['published'],
						'__LINK' => $version['__LINK']
					);
					$version_response[] = $versionsResponse;
				}
				$packResponce = array(
					'id' => $pack['id'],
					'name' => $pack['name'],
					'safeName' => $pack['safeName'],
					'versions' => array_reverse($version_response),
					'type' => $pack['type'],
					'description' => $pack['description'],
					'supportURL' => $pack['supportURL'],
					'websiteURL' => $pack['websiteURL']
				);
				$packs[] = $packResponse;
			}
			return getResponse(false, 200, null, $packs);
		} elseif($arguments[2] == "full" && $arguments[3] == "public") {
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$version_sql = mysql_query("select * from ". $pack['safeName']. "Version");
				$version_responce = array();
				while($version = mysql_fetch_array($version_sql)) {
					$versionsResponse = array(
						'version' => $version['version'],
						'minecraft' => $version['minecraft'],
						'published' => $version['published'],
						'__LINK' => $version['__LINK']
					);
					$version_response[] = $versionsResponse;
				}
				$packResponse = array(
					'id' => $pack['id'],
					'name' => $pack['name'],
					'safeName' => $pack['safeName'],
					'versions' => array_reverse($version_response),
					'type' => $pack['type'],
					'description' => $pack['description'],
					'supportURL' => $pack['supportURL'],
					'websiteURL' => $pack['websiteURL']
				);
				if($packResponse['type'] == "public") {
					$packs[] = $packResponse;
				}
			}
			return getResponse(false, 200, null, $packs);
		} elseif($arguments[2] == "full" && $arguments[3] == "semipublic") {
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$version_sql = mysql_query("select * from ". $pack['safeName']. "Version");
				$version_response = array();
				while($version = mysql_fetch_array($version_sql)) {
					$versionsResponse = array(
						'version' => $version['version'],
						'minecraft' => $version['minecraft'],
						'published' => $version['published'],
						'__LINK' => $version['__LINK']
					);
					$version_response[] = $versionsResponse;
				}
				$packResponse = array(
					'id' => $pack['id'],
					'name' => $pack['name'],
					'safeName' => $pack['safeName'],
					'versions' => array_reverse($version_response),
					'type' => $pack['type'],
					'description' => $pack['description'],
					'supportURL' => $pack['supportURL'],
					'websiteURL' => $pack['websiteURL']
				);
				if($packResponse['type'] == "semipublic") {
					$packs[] = $packResponse;
				}
			}
			return getResponse(false, 200, null, $packs);
		} elseif($arguments[2] == "full" && $arguments[3] == "private") {
			$pack_sql = mysql_query("select * from pack");
			$packs = array();
			$pack_array = null;
			while($pack = mysql_fetch_array($pack_sql)) {
				$version_sql = mysql_query("select * from ". $pack['safeName']. "Version");
				$version_response = array();
				while($version = mysql_fetch_array($version_sql)) {
					$versionsResponce = array(
						'version' => $version['version'],
						'minecraft' => $version['minecraft'],
						'published' => $version['published'],
						'__LINK' => $version['__LINK']
					);
					$version_response[] = $versionsResponse;
				}
				$packResponse = array(
					'id' => $pack['id'],
					'name' => $pack['name'],
					'safeName' => $pack['safeName'],
					'versions' => array_reverse($version_response),
					'type' => $pack['type'],
					'description' => $pack['description'],
					'supportURL' => $pack['supportURL'],
					'websiteURL' => $pack['websiteURL']
				);
				if($packResponse['type'] == "private") {
					$packs[] = $packResponse;
				}
			}
			return getResponse(false, 200, null, $packs);
		} else {
			return error($api_version, $api_versions);
		}
	}
	function stats($arguments, $api_version, $api_versions) {
		if($arguments[2] == "exe") { // /v1/stats/exe
			$stats_sql = mysql_query("select * from stats");
			$stats = array();
			$response = 0;
			while($stat = mysql_fetch_array($stats_sql)) {
				if($stat['option_name'] == "exe") {
					$response = intval($stat['option_value']);
				}
			}
		} elseif($arguments[2] == "zip") { // /v1/stats/zip
			$stats_sql = mysql_query("select * from stats");
			$stats = array();
			$response = 0;
			while($stat = mysql_fetch_array($stats_sql)) {
				if($stat['option_name'] == "zip") {
					$response = intval($stat['option_value']);
				}
			}
		} elseif($arguments[2] == "jar") { // /v1/stats/jar
			$stats_sql = mysql_query("select * from stats");
			$stats = array();
			$response = 0;
			while($stat = mysql_fetch_array($stats_sql)) {
				if($stat['option_name'] == "jar") {
					$response = intval($stat['option_value']);
				}
			}
		} elseif($arguments[2] == "all") { // /v1/stats/all
			$stats_sql = mysql_query("select * from stats");
			$stats = array();
			$response = 0;
			while($stat = mysql_fetch_array($stats_sql)) {
				if($stat['option_name'] == "exe" || $stat['option_name'] == "zip" || $stat['option_name'] == "jar") {
					$response = intval($response) + intval($stat['option_value']);
				}
			}
		} else {
			return error($api_version, $api_versions);
		}
		return getResponse(false, 200, null, $response);
	}
	function leaderboards($arguments, $api_version, $api_versions) {
		//TODO: do this
		return getResponse(false, 200, null, "Not complete yet, placeholder text");
	}
	function admin($arguments, $api_version, $api_versions) {
		//TODO: do this
		//This will also need a user system
		return getResponse(false, 200, null, "Not complete yet, placeholder text");
	}
	function psp($arguments, $api_version, $api_versions) { // not the most necessary part, but I want to do a full implementation :P
		//TODO: do this
		return getResponse(false, 200, null, "Not complete yet, placeholder text");
	}
	function networktest($arguments, $api_version, $api_versions) {
		//TODO: do this
		return getResponse(false, 200, null, "Not complete yet, placeholder text");
	}
?>
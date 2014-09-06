<?php
	// methods
	function getPack($arguments, $api_version, $api_versions) { // actually pack, but since there is a function in php called pack
		if(count($arguments) == 1 || count($arguments) == 2) {
			exit(error($api_version, $api_versions));
		} else {
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
					exit(error($api_version, $api_versions));
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
				
				$responce = array(
					'id' => $pack_array['id'],
					'name' => $pack_array['name'],
					'safeName' => $pack_array['safeName'],
					'type' => $pack_array['type'],
					'versions' => array_reverse($versions),
					'description' => $pack_array['description'],
					'supportURL' => $pack_array['supportURL'],
					'websiteURL' => $pack_array['websiteURL']
				);
				
				exit(getResponce(false, 200, null, $responce));
			} elseif(count($arguments) == 4) { // /v1/pack/name/version
				exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
			} else {
				exit(error($api_version, $api_versions));
			}
		}
	}
	function packs($arguments, $api_version, $api_versions) {
		//TODO: do this
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
	function stats($arguments, $api_version, $api_versions) {
		//TODO: do this
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
	function leaderboards($arguments, $api_version, $api_versions) {
		//TODO: do this
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
	function admin($arguments, $api_version, $api_versions) {
		//TODO: do this
		//This will also need a user system
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
	function psp($arguments, $api_version, $api_versions) { // not the most necessary part, but I want to do a full implementation :P
		//TODO: do this
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
	function networktest($arguments, $api_version, $api_versions) {
		//TODO: do this
		exit(getResponce(false, 200, null, "Not complete yet, placeholder text"));
	}
?>
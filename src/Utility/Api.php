<?php
namespace DemocracyApps\CNP\Utility;

class Api {
	public static function isApiCall ($uri)
	{
		$pos = strpos($uri, '/api');
		$isAPI = false;
		if ($pos !== false) {
			$isAPI = true;
		}
		return $isAPI;
	}
}
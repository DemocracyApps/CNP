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

	public static function compactMessages($messages) 
	{
		$msg = "";
		foreach ($messages->all() as $message) {
			$msg .= ' '.$message;
		}
		return $msg;
	}

}
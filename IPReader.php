<?php

namespace Utility;

use Exception;

class IPReader
{
	public static function IPAddress()
	{
		try {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else $ip = $_SERVER['REMOTE_ADDR'];

			return $ip;
		} catch (Exception $e) {
			$e = $e->getMessage();
			return $_SERVER['REMOTE_ADDR'];
		}
	}
}

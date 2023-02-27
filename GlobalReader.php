<?php

namespace Utility;

use Exception;

abstract class GlobalReader
{
	const GET_GLOBAL = 1;
	const POST_GLOBAL = 2;
	const SESSION_GLOBAL = 3;
	const COOKIE_GLOBAL = 4;

	private function search_in_global(int $global, array $search)
	{
		$global = $this->get_global($global);

		foreach ($search as $key) {
			if (array_key_exists($key, $global)) continue;
			return false;
		}

		return true;
	}

	private function get_global(int $global)
	{
		switch ($global) {
			case self::POST_GLOBAL:
				return $_POST;
			case self::GET_GLOBAL:
				return $_GET;
			case self::SESSION_GLOBAL:
				return $_SESSION;
			case self::COOKIE_GLOBAL:
				return $_COOKIE;
			default:
				throw new Exception("Undefined global type");
		}
	}

	public function postable()
	{
		return $this->search_in_global(self::POST_GLOBAL, func_get_args());
	}

	public function getable()
	{
		return $this->search_in_global(self::GET_GLOBAL, func_get_args());
	}

	public function sessionable()
	{
		return $this->search_in_global(self::SESSION_GLOBAL, func_get_args());
	}

	public function cookieable()
	{
		return $this->search_in_global(self::COOKIE_GLOBAL, func_get_args());
	}

	public function get(string $value, int $global = self::POST_GLOBAL)
	{
		$global = $this->get_global($global);

		return $global[$value];
	}

	public function getAsString(string $value, $acceptEmpty = false, int $global = self::POST_GLOBAL)
	{
		$string = (string) $this->get($value, $global);
		if (!$acceptEmpty && trim($value) === '') {
			throw new Exception("String value for '$value' is empty");
		}

		return $string;
	}

	public function getAsInt(string $value, $acceptZero = false, int $global = self::POST_GLOBAL)
	{
		$int = (int) $this->get($value, $global);

		if (!$acceptZero && $int === 0) throw new Exception("Integer value for '$value' is 0");

		if ($int < 0) throw new Exception("Value for '$value' is negative");

		return $int;
	}

	public function getAsFloat(string $value, $acceptZero = false, int $global = self::POST_GLOBAL)
	{
		$float = (float) $this->get($value, $global);

		if (!$acceptZero && $float === 0) throw new Exception("Floating point value for '$value' is 0");

		if ($float < 0) throw new Exception("Value for '$value' is negative");

		return $float;
	}

	public function getAsBool(string $value, int $global = self::POST_GLOBAL)
	{
		return (bool) $this->get($value, $global);
	}

	public function getAsJSON(string $value, int $global = self::POST_GLOBAL)
	{
		return json_decode($this->get($value, $global));
	}

	public function getAsJSONAssoc(string $value, int $global = self::POST_GLOBAL)
	{
		return json_decode($this->get($value, $global), true);
	}

	public function getFile(string $value)
	{
		return $_FILES[$value];
	}
}

<?php

namespace Utility;

class TokenGenerator
{
	public static function generate()
	{
		return md5(uniqid(mt_rand(0, 100000)));
	}
}

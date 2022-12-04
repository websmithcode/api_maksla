<?php

use Illuminate\Support\Str;

function parse_value_as_array(string $value)
{
	if (Str::isJson($value)) {
		return json_decode($value, true);
	} else {
		return array($value);
	}
}

<?php

namespace App\Domain\Common\models;

abstract class BaseDto
{
    use GetPropertiesTrait;
    function getAllowKeyNames() : array
    {
        return $this->getPropertyNames($this);
    }

    public function __construct($params = null)
    {
        if ($params == null) {
            return;
        }
        $KEY_NAMES = [];
        $arrayParameter = [];
        if (is_array($params)) {
            $KEY_NAMES = array_keys($params);
            $arrayParameter = $params;
        }

        if (is_object($params)) {
            $KEY_NAMES = array_keys(get_object_vars($params));
            $arrayParameter = (array)($params);
        }
        $allowKeys = $this->getAllowKeyNames();
        foreach($KEY_NAMES as $key) {
            if (in_array($key, $allowKeys))
                $this->{$key} = $arrayParameter[$key];
        }
    }
}
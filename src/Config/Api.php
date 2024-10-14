<?php

namespace Apiwpp\Config;

class Api
{

    public static bool $isDebug = false;

    public static string $endpoint = '';

    public static string $api_key = '';

    public static function setApikey(string $api_key)
    {
        self::$api_key = $api_key;
    }

    public static function setEndpoint(string $endpoint)
    {
        self::$endpoint = $endpoint;
    }

    public static function getEndpoint(): string
    {
        return self::$endpoint;
    }

    public static function getApikey()
    {
        return self::$api_key;
    }

    public static function setConfigs(string $apikey, string $endpoint, string $type_api = 'Evolution')
    {
        self::setApikey($apikey);
        self::setEndpoint($endpoint);
       
    }

    public static function debug(bool $isDebug = true)
    {
        self::$isDebug = $isDebug;
    }

}

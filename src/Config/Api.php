<?php

namespace Apiwpp\Config;

use Apiwpp\Api\Wuzapi\Device as Wuzapi;
use Apiwpp\Api\Evolution\Device as Evolution;

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

    public static function runType(string $type)
    {
        switch ($type) {

            case 'Wuzapi':
                Wuzapi::init();
                break;

            case 'Evolution':
                Evolution::init();
                break;

            default:
                Wuzapi::init();
                break;
        }
    }

    public static function setConfigs(string $apikey, string $endpoint, string $type_api = 'Wuzapi')
    {
        self::setApikey($apikey);
        self::setEndpoint($endpoint);
        self::runType($type_api);
    }

    public static function debug(bool $isDebug = true)
    {
        self::$isDebug = $isDebug;
    }

}

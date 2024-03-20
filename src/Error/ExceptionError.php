<?php

namespace Apiwpp\Error;

use Apiwpp\Config\Api;

class ExceptionError
{

    public static string $message = "";

    public static int $code = 200;

    public static bool $error = false;

    public static function setError(int $code = 200, string $message = "")
    {
        self::$error = true;
        self::$message = $message;
        self::$code = $code;

        if (Api::$isDebug) {
            echo '<pre>';
            var_dump(ExceptionError::getError());
            die;
        }

    }

    public static function json_validate(string $string): bool {
        json_decode($string);
    
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function getError(): object
    {

        if(self::json_validate(self::$message)){
            $message = json_decode(self::$message);
            if(isset($message->message)){
                if(self::json_validate($message->message)){
                    $message->message = json_decode($message->message);
                }
            }
        }else{
            $message = self::$message;
        }

        return (object) ['error' => self::$error, 'message' => $message, 'code' => self::$code];

    }

    public static function setCode(int $code)
    {
        self::$code = $code;
    }

    public static function getCode(): int
    {
        return self::$code;
    }

    public static function setMessage(string $message)
    {
        self::$message = $message;
    }

    public static function getMessage(): string
    {
        return self::$message;
    }

    public static function typeError(int $code = 200): string
    {
        switch ($code) {
            case 403:
                return "Not authorized";
                break;
            case 404:
                return "Not found";
                break;
            case 500:
                return "Internal server error";
                break;
            case 503:
                return "Unavailable service";
                break;
            case 504:
                return "Gateway Timeout";
                break;
            case 200:
                return "OK";
                break;
            default:
                return "Unknown";
                break;
        }
    }

}
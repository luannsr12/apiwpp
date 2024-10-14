<?php

namespace Apiwpp\Api\Evolution2;

use Apiwpp\Api\Evolution2\Device;
use Apiwpp\Error\ExceptionError;

class Account
{
    public static string $phoneValid = '';
    public static bool $isWhatsapp = false;
    public static string $accountName = '';
    public static string $accountStatus = '';

    private static function executeCurl(string $url, string $payload): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/' . $url . '/' . trim(Device::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . trim(Device::$instance),
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ['response' => $response, 'httpCode' => $httpCode];
    }

    private static function handleError(string $class, string $method, string $message, int $httpCode): void
    {
        ExceptionError::setError($httpCode, json_encode([
            'type' => 'Api response',
            'class' => $class,
            'method' => $method,
            'message' => $message,
        ]));
    }

    public static function checkPhone(string $phone)
    {
        try {
            $payload = json_encode(['numbers' => [trim($phone)]]);
            $result = self::executeCurl('chat/whatsappNumbers', $payload);
            $response = $result['response'];
            $httpCode = $result['httpCode'];

            if (!ExceptionError::json_validate($response)) {
                self::handleError(__CLASS__, __METHOD__, $response, $httpCode);
                return false;
            }

            if ($httpCode === 200) {
                $json = json_decode($response);
                if (isset($json[0]->exists) && $json[0]->exists) {
                    self::$phoneValid = explode('@', $json[0]->jid)[0];
                    self::$isWhatsapp = true;
                    return $json;
                } else {
                    self::handleError(__CLASS__, __METHOD__, 'Whatsapp account not found', 404);
                    return false;
                }
            } else {
                self::handleError(__CLASS__, __METHOD__, $response, $httpCode);
                return false;
            }
        } catch (\Exception $e) {
            self::handleError(__CLASS__, __METHOD__, $e->getMessage(), 500);
            return false;
        }
    }

    public static function getImageProfile(string $phone)
    {
        try {
            $payload = json_encode(['number' => trim($phone)]);
            $result = self::executeCurl('chat/fetchProfilePictureUrl', $payload);
            $response = $result['response'];
            $httpCode = $result['httpCode'];

            if (!ExceptionError::json_validate($response)) {
                self::handleError(__CLASS__, __METHOD__, $response, $httpCode);
                return false;
            }

            $json = json_decode($response);

            if (isset($json->profilePictureUrl) && $json->profilePictureUrl !== "") {
                return $json->profilePictureUrl;
            } else {
                self::handleError(__CLASS__, __METHOD__, $json->error ?? $response, $httpCode);
                return false;
            }
        } catch (\Exception $e) {
            self::handleError(__CLASS__, __METHOD__, $e->getMessage(), 500);
            return false;
        }
    }

    public static function detailsAccount(string $phone)
    {
        try {
            $payload = json_encode(['number' => trim($phone)]);
            $result = self::executeCurl('chat/fetchProfile', $payload);
            $response = $result['response'];
            $httpCode = $result['httpCode'];

            if (!ExceptionError::json_validate($response)) {
                self::handleError(__CLASS__, __METHOD__, $response, $httpCode);
                return false;
            }

            $json = json_decode($response);

            if (isset($json->numberExists) && $json->numberExists) {
                self::$accountStatus = $json->status ?? 'vazio';
                self::$accountName = $json->name ?? '';
                return $json;
            } else {
                self::handleError(__CLASS__, __METHOD__, $json->error ?? $response, $httpCode);
                return false;
            }
        } catch (\Exception $e) {
            self::handleError(__CLASS__, __METHOD__, $e->getMessage(), 500);
            return false;
        }
    }
}

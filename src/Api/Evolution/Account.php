<?php

namespace Apiwpp\Api\Evolution;

use Apiwpp\Api\Evolution\Device;
use Apiwpp\Error\ExceptionError;

class Account
{

    public static string $phoneValid = '';

    public static bool $isWhatsapp = false;

    public static string $accountName = '';

    public static string $accountStatus = '';

    public static function checkPhone(string $phone)
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/whatsappNumbers/' . trim(Device::$name_instance),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"numbers": ["' . trim($phone) . '"]}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'apikey: ' . trim(Device::$instance)
                ),
            )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }

            if ($httpCode == 200) {

                $json = json_decode($response);

                if (isset($json[0]->exists)) {
                    if ($json[0]->exists) {

                        self::$phoneValid = explode('@', $json[0]->jid)[0];
                        self::$isWhatsapp = $json[0]->exists;

                        return $json;

                    } else {
                        ExceptionError::setError(404, json_encode(['type' => 'notFound', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => 'Whatsapp account not found']));
                        return false;
                    }

                } else {
                    ExceptionError::setError(404, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $response]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function getImageProfile(string $phone)
    {

        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/fetchProfilePictureUrl/' . trim(Device::$name_instance),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{"number": "'.$phone.'"}',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'apikey: ' . trim(Device::$instance)
              ),
            ));
        

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'getImageProfile', 'message' => $response]));
                return false;
            }

            $json = json_decode($response);

            if (isset($json->profilePictureUrl)) {
                if ($json->profilePictureUrl != "") {

                    return $json->profilePictureUrl;

                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }


        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Account', 'method' => 'getImageProfile', 'message' => $e->getMessage()]));
            return false;
        }

    }

    public static function detailsAccount(string $phone)
    {
        try {

           $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/fetchProfile/' . trim(Device::$name_instance),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{"number": "'.$phone.'"}',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'apikey: ' . trim(Device::$instance)
              ),
            ));
        
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'detailsAccount', 'message' => $response]));
                return false;
            }


            $json = json_decode($response);

            if (isset($json->numberExists)) {
                if ($json->numberExists) {

                    self::$accountStatus = $json->status != "" ? $json->status : "vazio";
                    self::$accountName = $json->name != null ? $json->name : "";

                    return $json;

                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'detailsAccount', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Account', 'method' => 'detailsAccount', 'message' => $response]));
                return false;
            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Account', 'method' => 'detailsAccount', 'message' => $e->getMessage()]));
            return false;
        }
    }


}
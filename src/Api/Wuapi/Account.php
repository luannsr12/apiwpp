<?php

namespace Apiwpp\Api\Wuapi;

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

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/user/check',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{"Phone":["' . trim($phone) . '"]}',
                    CURLOPT_HTTPHEADER => array(
                        'Token: ' . trim(Device::$instance),
                        'Content-Type: application/json'
                    ),
                )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }


            $json = json_decode($response);

            if (isset ($json->success)) {
                if ($json->success) {

                    if (empty ($json->data->Users)) {
                        ExceptionError::setError(404, json_encode(['type' => 'notFound', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => 'Whatsapp account not found']));
                        return false;
                    }

                    if (isset ($json->data->Users[0]->JID, $json->data->Users[0]->IsInWhatsapp, $json->data->Users[0]->VerifiedName)) {

                        self::$phoneValid = explode('@', $json->data->Users[0]->JID)[0];
                        self::$isWhatsapp = $json->data->Users[0]->IsInWhatsapp;

                        return $json->data;

                    } else {
                        ExceptionError::setError(404, json_encode(['type' => 'notFound', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => 'Whatsapp account not found']));
                        return false;
                    }


                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function getImageProfile(string $phone)
    {

        try {

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/user/avatar',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => '{"Phone":"' . $phone . '"}',
                    CURLOPT_HTTPHEADER => array(
                        'Token: 29ef059740ddc59ad757',
                        'Content-Type: application/json'
                    ),
                )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'getImageProfile', 'message' => $response]));
                return false;
            }

            $json = json_decode($response);

            if (isset ($json->success)) {
                if ($json->success) {

                    if (!isset ($json->data->URL) ) {
                        ExceptionError::setError(404, json_encode(['type' => 'NotImage', 'class' => 'Api\Account', 'method' => 'getImageProfile', 'message' => 'Whatsapp account not found image']));
                        return false;
                    }

                    return $json->data->URL;

                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'checkPhone', 'message' => $response]));
                return false;
            }


        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Account', 'method' => 'getImageProfile', 'message' => $e->getMessage()]));
            return false;
        }

    }

    public static function detailsAccount(string $phone)
    {
        try {

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/user/info',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => '{"Phone":["' . trim($phone) . '"]}',
                    CURLOPT_HTTPHEADER => array(
                        'Token: ' . trim(Device::$instance),
                        'Content-Type: application/json'
                    ),
                )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!ExceptionError::json_validate($response)) {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'detailsAccount', 'message' => $response]));
                return false;
            }


            $json = json_decode($response);

            if (isset ($json->success)) {
                if ($json->success) {

                    if (empty ($json->data->Users)) {
                        ExceptionError::setError(404, json_encode(['type' => 'notFound', 'class' => 'Api\Account', 'method' => 'detailsAccount', 'message' => 'Whatsapp account not found']));
                        return false;
                    }

                    $userSelect = NULL;

                    foreach ($json->data->Users as $user) {
                        $userSelect = $user;
                        break;
                    }

                    self::$accountStatus = $userSelect->Status != "" ? $userSelect->Status : "vazio";
                    self::$accountName = $userSelect->VerifiedName != null ? $userSelect->VerifiedName : "";

                    return true;

                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'detailsAccount', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Account', 'method' => 'detailsAccount', 'message' => $response]));
                return false;
            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Account', 'method' => 'detailsAccount', 'message' => $e->getMessage()]));
            return false;
        }
    }


}
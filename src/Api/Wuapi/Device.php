<?php

namespace Apiwpp\Api\Wuapi;

use Apiwpp\Config\Api;
use Apiwpp\Error\ExceptionError;

class Device
{
   public static string $qrcode = '';

   public static string $instance = '';

   public static string $endpoint = '';

   public static bool $isAuth = false;

   public static string $apikey = '';

   public static bool $apiKeyAuth = false;

   public static function init()
   {
      $endpoint = Api::getEndpoint();
      if ($endpoint == NULL || $endpoint == "") {
         ExceptionError::setError(404, json_encode(['type' => 'Credentials', 'class' => 'Api\Device', 'method' => 'init', 'message' => 'API endpoint not defined']));
      } else {
         self::$endpoint = $endpoint;
      }

      $apikey = Api::getApikey();

      if ($apikey == NULL || $apikey == "") {
         ExceptionError::setError(404, json_encode(['type' => 'Credentials', 'class' => 'Api\Device', 'method' => 'init', 'message' => 'ApiKey not defined']));
      } else {
         self::$apikey = $apikey;
      }

   }

   public static function setInstance(string $instance)
   {
      self::$instance = $instance;
   }

   public static function getInstance()
   {
      return self::$instance;
   }

   public static function auth(bool $isApikey = false)
   {

      try {

         if ($isApikey && self::$apiKeyAuth) {
            return true;
         } else if (!$isApikey && self::$isAuth) {
            return true;
         }

         $token = $isApikey ? self::$apikey : self::$instance;

         if ($token == NULL) {
            $message = $isApikey ? "Invalid apikey" : "Invalid instance";
            ExceptionError::setError(403, json_encode(['type' => 'Credentials', 'class' => 'Api\Device', 'method' => 'auth', 'message' => $message]));
         }

         if (ExceptionError::$error) {
            return false;
         }

         self::start($token);

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/session/status',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 3,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim($token)
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode == 500) {
               $json = json_decode($response);
               if (isset ($json->error)) {
                  if ($json->error == "Already Loggedin") {

                     if ($isApikey) {
                        self::$apiKeyAuth = true;
                     } else {
                        self::$isAuth = true;
                     }

                     return true;
                  }
               }
            }

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'auth', 'message' => $response]));
               return false;
            }

            if ($isApikey) {
               self::$apiKeyAuth = true;
            } else {
               self::$isAuth = true;
            }

            return true;

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'auth', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'auth', 'message' => $e->getMessage()]));
         return false;
      }

   }

   public static function start($token)
   {

      try {

         if (ExceptionError::$error) {
            return false;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/session/connect',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_POSTFIELDS => '{"Subscribe":["Message"],"Immediate":false}',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim($token),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         curl_close($curl);

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'start', 'message' => $e->getMessage()]));
      }

   }

   public static function getQrcode()
   {
      return self::$qrcode;
   }

   public static function create(string $token_device, string $name_device)
   {

      try {
         if ($name_device == '' || $name_device == '') {
            ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => 'Token device and Name device is required']));
         }

         if (ExceptionError::$error) {
            return false;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/devices/create?name=' . trim($name_device) . '&token=' . trim($token_device),
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$apikey),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200 && $httpCode != 201) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => $response]));
               return false;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => $response]));
               return false;
            }

            $json = json_decode($response);

            if (isset ($json->success)) {
               if ($json->success == true) {

                  if (isset ($json->data->name)) {
                     if ($json->data->name != "") {
                        return true;
                     } else {
                        ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => 'Device not created']));
                        return false;
                     }
                  } else {
                     ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => 'Device not created']));
                     return $json;
                  }

               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => $json->error]));
                  return $json;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'create', 'message' => 'Device not created']));
               return $json;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'create', 'message' => $e->getMessage()]));
            return false;
         }
      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'create', 'message' => $e->getMessage()]));
         return false;
      }

   }

   public static function getWebhook()
   {
      try {

         if (ExceptionError::$error) {
            return NULL;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/webhook',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$instance),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         if ($httpCode == 200) {
            if (ExceptionError::json_validate($response)) {
               return json_decode($response)->data;
            } else {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'getWebhook', 'message' => $response]));
               return NULL;
            }
         } else {
            ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'getWebhook', 'message' => $response]));
            return NULL;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'getWebhook', 'message' => $e->getMessage()]));
         return NULL;
      }
   }

   public static function setWebhook(string $webhook)
   {
      try {

         if (ExceptionError::$error) {
            return false;
         }

         if (filter_var($webhook, FILTER_VALIDATE_URL) === FALSE) {
            ExceptionError::setError(500, json_encode(['type' => 'Url invalid', 'class' => 'Api\Device', 'method' => 'create', 'message' => $webhook . ' not valid url']));
            return false;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/webhook',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_POSTFIELDS => '{"webhookURL":"' . $webhook . '"}',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$instance),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         if ($httpCode == 200) {
            return true;
         } else {
            ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'setWebhook', 'message' => $response]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'setWebhook', 'message' => $e->getMessage()]));
         return false;
      }

   }

   public static function loadQr()
   {

      try {

         self::auth();

         if (ExceptionError::$error) {
            return NULL;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/session/qr',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$instance)
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            $json = json_decode($response);

            if (isset ($json->success)) {

               if ($json->success == false) {

                  if (isset ($json->error)) {
                     ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $json->error]));
                     return NULL;
                  } else {
                     ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $response]));
                     return NULL;
                  }

               } else if ($json->success == true) {

                  if (isset ($json->data->QRCode)) {
                     self::$qrcode = $json->data->QRCode;
                     return true;
                  } else {
                     ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $response]));
                     return NULL;
                  }

               } else {
                  ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $response]));
                  return NULL;
               }
            } else {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $response]));
               return NULL;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $e->getMessage()]));
            return NULL;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'loadQr', 'message' => $e->getMessage()]));
         return NULL;
      }


   }

   public static function isConnected(): bool
   {

      if (ExceptionError::$error) {
         return false;
      }

      $curl = curl_init();

      curl_setopt_array(
         $curl,
         array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/session/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
               'Token: ' . trim(self::$instance),
               'Content-Type: application/json'
            ),
         )
      );

      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      try {

         if ($httpCode != 200) {
            ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'isConnected', 'message' => $response]));
            return false;
         }

         if (!json_decode($response)) {
            ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'isConnected', 'message' => $response]));
            return false;
         }

         $json = json_decode($response);

         if (isset ($json->success)) {
            if ($json->success == true) {

               if ($json->data->Connected == false && $json->data->LoggedIn == false) {
                  return false;
               } else if ($json->data->Connected == true && $json->data->LoggedIn == false) {
                  return false;
               } else if ($json->data->Connected == false && $json->data->LoggedIn == true) {
                  return false;
               } else if ($json->data->Connected == true && $json->data->LoggedIn == true) {
                  return true;
               } else {
                  return false;
               }

            } else {
               return false;
            }
         } else {
            return false;
         }


      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'isConnected', 'message' => $e->getMessage()]));
         return false;
      }

   }

   public static function logout()
   {

      try {

         if (ExceptionError::$error) {
            return NULL;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/session/logout',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$instance)
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $response]));
               return NULL;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $response]));
               return NULL;
            }


            $json = json_decode($response);

            if (isset ($json->success)) {
               if ($json->success == true) {
                  return true;
               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $json->error]));
                  return false;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $response]));
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'logout', 'message' => $e->getMessage()]));
         return false;
      }
   }

   public static function list()
   {
      try {

         if (ExceptionError::$error) {
            return NULL;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/devices/list',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
                  'Token: ' . trim(self::$apikey),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'list', 'message' => $response]));
               return NULL;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'list', 'message' => $response]));
               return NULL;
            }

            $json = json_decode($response);

            if (isset ($json->success)) {
               if ($json->success == true) {

                  if (isset ($json->data)) {
                     return $json->data;
                  } else {
                     ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'list', 'message' => 'Not devices']));
                     return $json;
                  }

               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'list', 'message' => $json->error]));
                  return $json;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Device', 'method' => 'list', 'message' => 'Device not created']));
               return $json;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'list', 'message' => $e->getMessage()]));
            return NULL;
         }
      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Device', 'method' => 'list', 'message' => $e->getMessage()]));
         return NULL;
      }

   }

}
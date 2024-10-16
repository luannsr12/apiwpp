<?php

namespace Apiwpp\Api\Evolution;

use Apiwpp\Config\Api;
use Apiwpp\Error\ExceptionError;

class Device
{
   public static string $qrcode = '';

   public static string $instance = '';

   public static string $name_instance = '';

   public static string $endpoint = '';

   public static bool $isAuth = false;

   public static string $apikey = '';

   public static bool $apiKeyAuth = false;

   public static function autoload()
   {
      $endpoint = Api::getEndpoint();
      if ($endpoint == NULL || $endpoint == "") {
         ExceptionError::setError(404, json_encode(['type' => 'Credentials', 'class' => 'Api\Evolution\Device', 'method' => 'init', 'message' => 'API endpoint not defined']));
      } else {
         self::$endpoint = $endpoint;
      }

      $apikey = Api::getApikey();

      if ($apikey == NULL || $apikey == "") {
         ExceptionError::setError(404, json_encode(['type' => 'Credentials', 'class' => 'Api\Evolution\Device', 'method' => 'init', 'message' => 'ApiKey not defined']));
      } else {
         self::$apikey = $apikey;
      }

   }

   public static function setInstance(string $instance, string $name_instance)
   {
      self::$instance = $instance;
      self::$name_instance = $name_instance;
   }

   public static function getInstance()
   {
      return self::$instance;
   }

   public static function getNameInstance()
   {
      return self::$name_instance;
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
            ExceptionError::setError(403, json_encode(['type' => 'Credentials', 'class' => 'Api\Evolution\Device', 'method' => 'auth', 'message' => $message]));
         }

         if (ExceptionError::$error) {
            return false;
         }

         $name_instance = self::$name_instance;

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/connectionState/' . trim($name_instance),
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 3,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'GET',
               CURLOPT_HTTPHEADER => array(
                  'apikey: ' . trim($token)
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode == 200) {
               $json = json_decode($response);

               if (isset($json->instance->state)) {
                  if ($json->instance->state == 'open') {

                     if ($isApikey) {
                        self::$apiKeyAuth = true;
                     } else {
                        self::$isAuth = true;
                     }

                     return true;

                  } else {
                     return false;
                  }
               } else {
                  return false;
               }

            }

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'auth', 'message' => $response]));
               return false;
            }

            if ($isApikey) {
               self::$apiKeyAuth = true;
            } else {
               self::$isAuth = true;
            }

            return true;

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'auth', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'auth', 'message' => $e->getMessage()]));
         return false;
      }

   }


   public static function getQrcode()
   {
      return self::$qrcode;
   }

   public static function create(string $token_device, string $name_device, string $integration = 'WHATSAPP-BAILEYS')
   {

      try {
         if ($name_device == '' || $name_device == NULL) {
            ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => 'Token device and Name device is required']));
         }

         if (ExceptionError::$error) {
            return false;
         }

         $curl = curl_init();

         curl_setopt_array(
            $curl,
            array(
               CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/create',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_POSTFIELDS => '{
                  "instanceName": "' . trim($name_device) . '",
                  "token": "' . trim($token_device) . '", 
                  "qrcode": false,
                  "mobile": false,
                  "integration": "' . trim($integration) . '" 
              }',
               CURLOPT_HTTPHEADER => array(
                  'apikey: ' . trim(self::$apikey),
                  'Content-Type: application/json'
               ),
            )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200 && $httpCode != 201) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => $response]));
               return false;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => $response]));
               return false;
            }

            $json = json_decode($response);

            if (isset($json->instance)) {
               if ($json->instance->status == 'created') {
                  return true;
               } else {
                  ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => 'Device not created']));
                  return false;
               }

            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => 'Device not created']));
               return $json;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => $e->getMessage()]));
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

         curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/webhook/find/' . trim(self::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
               'apikey: ' . trim(self::$instance)
            ),
         )
         );


         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         if ($httpCode == 200) {
            if (ExceptionError::json_validate($response)) {
               return json_decode($response);
            } else {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'getWebhook', 'message' => $response]));
               return NULL;
            }
         } else {
            ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'getWebhook', 'message' => $response]));
            return NULL;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'getWebhook', 'message' => $e->getMessage()]));
         return NULL;
      }
   }

   public static function setWebhook(string $webhook)
   {
      try {

         if (ExceptionError::$error) {
            return false;
         }

         if (filter_var($webhook, FILTER_VALIDATE_URL) === FALSE && $webhook != "disabled") {
            ExceptionError::setError(500, json_encode(['type' => 'Url invalid', 'class' => 'Api\Evolution\Device', 'method' => 'create', 'message' => $webhook . ' not valid url']));
            return false;
         }

         $disabled_webhook = '';

         if ($webhook == "disabled") {
            $disabled_webhook = '"enabled": false,';
         }


         $curl = curl_init();

         curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/webhook/set/' . trim(self::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "url": "' . $webhook . '",
             "webhook_by_events": false,
             "webhook_base64": false,
             ' . $disabled_webhook . '
             "events": [
                 "QRCODE_UPDATED",
                 "MESSAGES_UPSERT",
                 "MESSAGES_UPDATE",
                 "MESSAGES_DELETE",
                 "SEND_MESSAGE",
                 "CONNECTION_UPDATE",
                 "CALL"
             ]    
         }',
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json',
               'apikey: ' . trim(self::$instance)
            ),
         )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         if ($httpCode == 200 || $httpCode == 201) {
            return true;
         } else {
            ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'setWebhook', 'message' => $response]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'setWebhook', 'message' => $e->getMessage()]));
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

         curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/connect/' . trim(self::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
               'apikey: ' . trim(self::$instance)
            ),
         )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            $json = json_decode($response);

            if (isset($json->base64)) {

               if ($json->base64 != '' && $json->base64 != NULL) {

                  self::$qrcode = $json->base64;
                  return true;

               } else {
                  ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'loadQr', 'message' => $response]));
                  return NULL;
               }
            } else {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'loadQr', 'message' => $response]));
               return NULL;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'loadQr', 'message' => $e->getMessage()]));
            return NULL;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'loadQr', 'message' => $e->getMessage()]));
         return NULL;
      }


   }

   public static function isConnected(): bool
   {

      if (ExceptionError::$error) {
         return false;
      }


      $name_instance = self::$name_instance;

      $curl = curl_init();

      curl_setopt_array(
         $curl,
         array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/connectionState/' . trim($name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
               'apikey: ' . trim(self::$instance)
            ),
         )
      );

      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      try {

         if (!json_decode($response)) {
            ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'isConnected', 'message' => $response]));
            return false;
         }

         $json = json_decode($response);

         if (isset($json->instance->state)) {
            if ($json->instance->state == 'open') {

               self::$isAuth = true;
               return true;

            } else {
               return false;
            }
         }

         return false;

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'isConnected', 'message' => $e->getMessage()]));
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

         curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/logout/' . trim(self::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
               'apikey: ' . trim(self::$instance)
            ),
         )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $response]));
               return NULL;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $response]));
               return NULL;
            }


            $json = json_decode($response);

            if (isset($json->status)) {
               if ($json->status == 'SUCCESS') {
                  return true;
               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $json->error]));
                  return false;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $response]));
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'logout', 'message' => $e->getMessage()]));
         return false;
      }
   }

   public static function delete()
   {

      try {

         if (ExceptionError::$error) {
            return NULL;
         }


         $curl = curl_init();

         curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim(self::$endpoint, '/') . '/instance/delete/' . trim(self::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
               'apikey: ' . trim(self::$instance)
            ),
         )
         );

         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         curl_close($curl);

         try {

            if ($httpCode != 200) {
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $response]));
               return NULL;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $response]));
               return NULL;
            }

            $json = json_decode($response);

            if (isset($json->status)) {
               if ($json->status == 'SUCCESS') {
                  return true;
               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $json->error]));
                  return false;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $response]));
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $e->getMessage()]));
            return false;
         }

      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'delete', 'message' => $e->getMessage()]));
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
               ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => $response]));
               return NULL;
            }

            if (!json_decode($response)) {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => $response]));
               return NULL;
            }

            $json = json_decode($response);

            if (isset($json->success)) {
               if ($json->success == true) {

                  if (isset($json->data)) {
                     return $json->data;
                  } else {
                     ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => 'Not devices']));
                     return $json;
                  }

               } else {
                  ExceptionError::setError($json->code, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => $json->error]));
                  return $json;
               }
            } else {
               ExceptionError::setError(500, json_encode(['type' => 'Api response', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => 'Device not created']));
               return $json;
            }

         } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => $e->getMessage()]));
            return NULL;
         }
      } catch (\Exception $e) {
         ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Evolution\Device', 'method' => 'list', 'message' => $e->getMessage()]));
         return NULL;
      }

   }

}

Device::autoload();
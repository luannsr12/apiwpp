<?php

namespace Apiwpp\Api\Wuapi;

use Apiwpp\Api\Device;
use Apiwpp\Error\ExceptionError;
use Apiwpp\Files\Process;

class Message
{

    public static string $lastIdMessage = '';

    public static bool $isSend = false;

    public static string $typeSend = 'text'; // text, video, audio, document, image, caption (text+image) or (text+video)

    public static array $types = ['text', 'video', 'audio', 'document', 'image'];

    public static string $caption = '';

    public static string $phone = '';

    public static string $text = '';

    public static string $fileUrl = '';


    public static function createId(){
        $idMessage = uniqid();
        self::$lastIdMessage = $idMessage;
    }

    public static function send(){

        if(self::$typeSend != "text"){
            if(self::$fileUrl == ""){
                ExceptionError::setError(401, json_encode(['type' => 'fileUrl', 'class' => 'Api\Message', 'method' => 'send', 'message' =>  'fileUrl is required to type ' . self::$typeSend]));
                return false;
            }
        }

        self::createId();

        if(self::$typeSend == "text"){
            self::sendText();
        }else if(self::$typeSend == "audio"){
            self::sendAudio();
        }else if(self::$typeSend == "document"){
            self::sendDocument();
        }else if(self::$typeSend == "image"){
            self::sendImage();
        }else if(self::$typeSend == "video"){
            self::sendVideo();
        }else{
            ExceptionError::setError(401, json_encode(['type' => 'typeSend', 'class' => 'Api\Message', 'method' => 'send', 'message' =>  'type send not found']));
            return false;
        }

        return self::$isSend;

    }

    public static function caption(string $caption = ""){
        self::$caption = $caption;
    }

    public static function fileUrl(string $url = ""){
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            ExceptionError::setError(500, json_encode(['type' => 'fileUrl', 'class' => 'Api\Message', 'method' => 'fileUrl', 'message' =>  'fileUrl not valid url']));
            return false;
         }

         return self::$fileUrl = $url;

    }

    public static function type(string $type = ""){
        if(!in_array($type, self::$types)){
            ExceptionError::setError(404, json_encode(['type' => 'TypesMessage', 'class' => 'Api\Message', 'method' => 'type', 'message' => 'Type send not found']));
        }

        self::$typeSend = $type;
    }

    public static function text(string $text = ""){
         self::$text = $text;
    }

    public static function phone(string $phone = ""){
        self::$phone = $phone;
   }


    public static function sendDocument()
    {
        try {

            Process::processFile(self::$fileUrl);
            $base64 = Process::base64();

            if($base64){

                $fileName = Process::$fileName;

                $data = array(
                    "Id"    => self::$lastIdMessage,
                    "Phone" => self::$phone,
                    "Document" => $base64,
                    'FileName' => $fileName
                );

                $postdata = json_encode($data);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/send/document',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postdata,
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim(Device::$instance),
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                
                if (!ExceptionError::json_validate($response)) {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendDocument', 'message' => $response]));
                    return false;
                }
    
                $json = json_decode($response);
    
                if (isset ($json->success)) {
                    if ($json->success) {
    
                        self::$isSend = true;
                        return true;
    
                    } else {
                        ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendDocument', 'message' => $json->error]));
                        return false;
                    }
    
                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendDocument', 'message' => $response]));
                    return false;
                }

            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendDocument', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function sendVideo()
    {
        try {

            Process::processFile(self::$fileUrl);
            $base64 = Process::base64();

            if($base64){

                $data = array(
                    "Id"    => self::$lastIdMessage,
                    "Phone" => self::$phone,
                    "Video" => $base64
                );
                
                self::$caption != "" ? $data['Caption'] = self::$caption : NULL;

                $postdata = json_encode($data);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/send/video',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postdata,
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim(Device::$instance),
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                
                if (!ExceptionError::json_validate($response)) {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendVideo', 'message' => $response]));
                    return false;
                }
    
                $json = json_decode($response);
    
                if (isset ($json->success)) {
                    if ($json->success) {
    
                        self::$isSend = true;
                        return true;
    
                    } else {
                        ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendVideo', 'message' => $json->error]));
                        return false;
                    }
    
                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendVideo', 'message' => $response]));
                    return false;
                }

            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendVideo', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function sendImage()
    {
        try {

            Process::processFile(self::$fileUrl);
            $base64 = Process::base64();

            if($base64){

                $data = array(
                    "Id"    => self::$lastIdMessage,
                    "Phone" => self::$phone,
                    "Image" => $base64
                );

                self::$caption != "" ? $data['Caption'] = self::$caption : NULL;

                $postdata = json_encode($data);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/send/image',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postdata,
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim(Device::$instance),
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                
                if (!ExceptionError::json_validate($response)) {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendImage', 'message' => $response]));
                    return false;
                }
    
                $json = json_decode($response);
    
                if (isset ($json->success)) {
                    if ($json->success) {
    
                        self::$isSend = true;
                        return true;
    
                    } else {
                        ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendImage', 'message' => $json->error]));
                        return false;
                    }
    
                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendImage', 'message' => $response]));
                    return false;
                }

            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendImage', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function sendAudio()
    {
        try {

            Process::processFile(self::$fileUrl);
            $base64 = Process::base64();

            if($base64){

                if(Process::$mimeType != "audio/ogg"){
                    ExceptionError::setError(500, json_encode(['type' => 'Format', 'class' => 'Api\Message', 'method' => 'sendImage', 'sendAudio' => 'Audio must be ogg']));
                    return false;
                }

                $data = array(
                    "Id"    => self::$lastIdMessage,
                    "Phone" => self::$phone,
                    "Audio" => $base64
                );

                $postdata = json_encode($data);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/send/audio',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postdata,
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim(Device::$instance),
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                
                if (!ExceptionError::json_validate($response)) {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendAudio', 'message' => $response]));
                    return false;
                }
    
                $json = json_decode($response);
    
                if (isset ($json->success)) {
                    if ($json->success) {
    
                        self::$isSend = true;
                        return true;
    
                    } else {
                        ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendAudio', 'message' => $json->error]));
                        return false;
                    }
    
                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendAudio', 'message' => $response]));
                    return false;
                }

            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendAudio', 'message' => $e->getMessage()]));
            return false;
        }
    }

    public static function sendText()
    {
        try {

            $data = array(
                "Id"    =>  self::$lastIdMessage,
                "Phone" => self::$phone,
                "Body"  => self::$text
            );

            $postdata = json_encode($data);

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => rtrim(Device::$endpoint, '/') . '/chat/send/text',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 1,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $postdata,
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
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendText', 'message' => $response]));
                return false;
            }

            $json = json_decode($response);

            if (isset ($json->success)) {
                if ($json->success) {

                    self::$isSend = true;
                    return true;

                } else {
                    ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendText', 'message' => $json->error]));
                    return false;
                }

            } else {
                ExceptionError::setError($httpCode, json_encode(['type' => 'Api response', 'class' => 'Api\Message', 'method' => 'sendText', 'message' => $response]));
                return false;
            }

        } catch (\Exception $e) {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendText', 'message' => $e->getMessage()]));
            return false;
        }
    }

}
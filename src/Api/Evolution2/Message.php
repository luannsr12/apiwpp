<?php

namespace Apiwpp\Api\Evolution2;

use Apiwpp\Error\ExceptionError;
use Apiwpp\Files\Process;

class Message
{
    public static string $lastIdMessage = '';
    public static bool $isSend = false;
    private static string $typeSend = 'text';
    private static string $caption = '';
    private static string $phone = '';
    private static string $text = '';
    private static string $fileUrl = '';

    private const ERROR_INVALID_FILE_URL = 'fileUrl not valid url';
    private const ERROR_REQUIRED_FILE_URL = 'fileUrl is required for type ';
    private const ERROR_TYPE_NOT_FOUND = 'Type send not found';
    private const API_ENDPOINT_TEMPLATE = '/message/sendMedia/';
    private const API_AUDIO_ENDPOINT = '/message/sendWhatsAppAudio/';
    private const MEDIA_TYPES_WITH_FILE_URL = ['video', 'audio', 'document', 'image'];

    private static array $types = ['text', 'video', 'audio', 'document', 'image'];

    public static function createId(): void
    {
        self::$lastIdMessage = uniqid();
    }

    public static function send(): bool
    {
        if (self::requiresFileUrl() && empty(self::$fileUrl)) {
            return self::handleError(401, 'fileUrl', 'send', self::ERROR_REQUIRED_FILE_URL . self::$typeSend);
        }

        self::createId();
        switch (self::$typeSend) {
            case 'text':
                return self::sendText();
            case 'audio':
                return self::sendAudio();
            case 'document':
                return self::sendDocument();
            case 'image':
                return self::sendImage();
            case 'video':
                return self::sendVideo();
            default:
                return self::handleError(401, 'typeSend', 'send', self::ERROR_TYPE_NOT_FOUND);
        }
    }

    public static function caption(string $caption = ''): self
    {
        self::$caption = $caption;
        return new self();
    }

    public static function fileUrl(string $url = ''): self
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            self::handleError(500, 'fileUrl', 'setFileUrl', self::ERROR_INVALID_FILE_URL);
        }
        self::$fileUrl = $url;
        return new self();
    }

    public static function type(string $type = ''): self
    {
        if (!in_array($type, self::$types, true)) {
            self::handleError(404, 'TypesMessage', 'setType', self::ERROR_TYPE_NOT_FOUND);
        } else {
            self::$typeSend = $type;
        }
        return new self();
    }

    public static function text(string $text = ''): self
    {
        self::$text = $text;
        return new self();
    }

    public static function phone(string $phone = ''): self
    {
        self::$phone = $phone;
        return new self();
    }

    private static function requiresFileUrl(): bool
    {
        return in_array(self::$typeSend, self::MEDIA_TYPES_WITH_FILE_URL, true);
    }

    private static function handleError(int $code, string $type, string $method, string $message): bool
    {
        ExceptionError::setError($code, json_encode([
            'type' => $type,
            'class' => __CLASS__,
            'method' => $method,
            'message' => $message
        ]));
        return false;
    }

    private static function sendRequest(array $data, string $url): bool
    {
        $postdata = json_encode($data);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => rtrim(Device::$endpoint, '/') . $url . trim(Device::$name_instance),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . trim(Device::$instance)
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return self::processResponse($response, $httpCode);
    }

    private static function processResponse($response, int $httpCode): bool
    {
        if (!ExceptionError::json_validate($response)) {
            return self::handleError($httpCode, 'Api response', debug_backtrace()[1]['function'], $response);
        }

        $json = json_decode($response);
        if (isset($json->key->id) && $json->key->id !== '') {
            self::$isSend = true;
            self::$lastIdMessage = $json->key->id;
            return true;
        }

        return self::handleError($httpCode, 'Api response', debug_backtrace()[1]['function'], $json->error ?? $response);
    }

    public static function sendDocument(): bool
    {
        Process::processFile(self::$fileUrl);
        $fileName = Process::$fileName;

        $data = [
            'number' => self::$phone,
            'media' => self::$fileUrl,
            'fileName' => $fileName,
            'mediatype' => 'document',
            'delay' => 1200,
        ];

        if (!empty(self::$caption)) {
            $data['mediaMessage']['caption'] = self::$caption;
        }

        return self::sendRequest($data, self::API_ENDPOINT_TEMPLATE);
    }

    public static function sendVideo(): bool
    {
        $data = [
            'number' => self::$phone,
            'mediatype' => 'video',
            'media' => self::$fileUrl,
        ];

        if (!empty(self::$caption)) {
            $data['caption'] = self::$caption;
        }

        return self::sendRequest($data, self::API_ENDPOINT_TEMPLATE);
    }

    public static function sendImage(): bool
    {
        $data = [
            'number' => self::$phone,
            'media' => self::$fileUrl,
            'mediatype' => 'image',
            'delay' => 1200,
        ];

        if (!empty(self::$caption)) {
            $data['caption'] = self::$caption;
        }

        return self::sendRequest($data, self::API_ENDPOINT_TEMPLATE);
    }

    public static function sendAudio(): bool
    {
        $data = [
            'number' => self::$phone,
            'audio' => self::$fileUrl,
            'delay' => 1200,
            'encoding' => true,
        ];

        return self::sendRequest($data, self::API_AUDIO_ENDPOINT);
    }

    private static function sendText(): bool
    {
        $data = [
            'number' => self::$phone,
            'text' => self::$text,
            'delay' => 1200,
            'linkPreview' => true
            
        ];

        return self::sendRequest($data, '/message/sendText/');
    }
}

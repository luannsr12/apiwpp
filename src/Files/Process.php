<?php

namespace Apiwpp\Files;

use Apiwpp\Error\ExceptionError;

class Process
{
    public static string $fileUrl = "";
    public static string $fileName = "";
    public static string $mimeType = "";
    public static string $base64 = "";
    public static array $metadata = [];
    public static string $extension = "";

    // Função para baixar o arquivo da URL fornecida
    public static function content()
    {
        // Tenta obter o conteúdo do arquivo
        $fileContent = file_get_contents(self::$fileUrl);

        // Verifica se foi possível obter o conteúdo do arquivo
        if ($fileContent !== false) {
            return $fileContent;
        } else {
            ExceptionError::setError(500, json_encode(['type' => 'Exception', 'class' => 'Api\Message', 'method' => 'sendAudio', 'message' => 'Download file fail']));
        }
    }

    public static function saveFile()
    {

        $tempDir = __DIR__ . '/tmp';

        $tempFilename = tempnam($tempDir, 'downloaded_file');

        // Tente baixar e salvar o arquivo
        if (copy(self::$fileUrl, $tempFilename)) {
            return $tempFilename;
        } else {
            throw new \Exception("Erro ao baixar o arquivo.");
        }

    }

    // Função para obter metadados do arquivo
    public static function getFileMetadata($filename)
    {
        // Obtém o tamanho do arquivo em bytes
        $size = filesize($filename);

        // Obtém a data de modificação do arquivo
        $modifiedTime = filemtime($filename);

        // Obtém a data de criação do arquivo (não disponível em todos os sistemas operacionais)
        $creationTime = filectime($filename);

        // Obtém o sistema operacional do arquivo (não disponível em todos os sistemas operacionais)
        $operatingSystem = php_uname('s');

        // Obtém o software usado para criar o arquivo (não disponível para todos os tipos de arquivo)
        $softwareUsed = 'N/A';

        // Obtém o nome do usuário do arquivo (não disponível em todos os sistemas operacionais)
        $user = 'N/A';

        // Obtém a localização do arquivo (não disponível em todos os sistemas operacionais)
        $location = 'N/A';

        // Coloque os metadados em um array associativo
        $metadata = array(
            'Size' => $size,
            'ModifiedTime' => $modifiedTime,
            'CreationTime' => $creationTime,
            'OperatingSystem' => $operatingSystem,
            'SoftwareUsed' => $softwareUsed,
            'User' => $user,
            'Location' => $location
        );

        return $metadata;

    }

    public static function getFileName($fileUrl){
        $stripped_url = preg_replace('/\\?.*/', '', $fileUrl);
        $filename = basename($stripped_url);
        self::$fileName = $filename;
        return $filename;
    }

    // Função para codificar o conteúdo do arquivo em base64
    public static function encodeFileToBase64($fileContent)
    {
        $base64Data = base64_encode($fileContent);
        return $base64Data;
    }

    // Função para obter o tipo MIME com base na extensão do arquivo
    public static function getMimeType($filename)
    {
        // Obtenha a extensão do arquivo
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        self::$extension = $extension;

        // Mapeamento de extensões para tipos MIME
        $mimeTypes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'application/octet-stream',
            'mp3' => 'audio/mpeg',
            'ogg' => 'audio/ogg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'wmv' => 'video/x-ms-wmv',
            'pdf' => 'application/octet-stream',
            'doc' => 'application/octet-stream',
            'docx' => 'application/octet-stream',
            'xls' => 'application/octet-stream',
            'xlsx' => 'application/octet-stream',
            'ppt' => 'application/octet-stream',
            'pptx' => 'application/octet-stream',
        );

        // Retorne o tipo MIME correspondente à extensão do arquivo, ou 'application/octet-stream' se não for encontrado
        return isset ($mimeTypes[$extension]) ? $mimeTypes[$extension] : 'application/octet-stream';
    }

    // Função para capturar metadados, baixar o arquivo e codificar em base64
    public static function processFile($fileUrl)
    {
        self::$fileUrl = $fileUrl;

        // conteudo do arquivo
        $fileContent = self::content();

        // salva o arquivo temporamente
        $tempFilename = self::saveFile();

        self::$mimeType = self::getMimeType($fileUrl);

        self::getFileName($fileUrl);

        // Codifique o conteúdo do arquivo em base64
        self::$base64 = self::encodeFileToBase64($fileContent);

        // Obtenha e armazene metadados do arquivo
        self::$metadata = self::getFileMetadata($tempFilename);

        unlink($tempFilename);
    }

    public static function base64()
    {
        return "data:" . self::$mimeType . ";base64," . self::$base64;
    }

    public static function mimeType()
    {
        return self::$mimeType;
    }

    public static function metadata()
    {
        return self::$metadata;
    }

    public static function location()
    {
        return self::$metadata['Location'];
    }

    public static function size()
    {
        return self::$metadata['Size'];
    }

    public static function software()
    {
        return self::$metadata['SoftwareUsed'];
    }

    public static function updated()
    {
        return self::$metadata['ModifiedTime'];
    }

    public static function created()
    {
        return self::$metadata['CreationTime'];
    }

    public static function systemOS()
    {
        return self::$metadata['OperatingSystem'];
    }

    public static function username()
    {
        return self::$metadata['User'];
    }

}


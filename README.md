## SDK PHP Evolution Api Whatsapp

Api Evolution: [Evolution-api](https://doc.evolution-api.com/v2/)

<hr>

SDK version: 2.0.4 <br />
PHP Version: >= 8.2

## Funções disponíveis

- Envio de video
- Envio de audio
- Envio de documentos
- Envio de imagens
- Envio de texto
- Checagem do número do whatsapp
- Imagem do perfil
- Detalhes do perfil
- Cria instância
- Desconecta instância
- Deleta a instância

## Instalação composer

```bash
 composer require luannsr12/apiwpp
```

#### Evolution Version >= 2.1.1
```php
 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Account;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

```

#### Evolution Version anterior a versão 2
```php
 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution\Account;
 use Apiwpp\Api\Evolution\Device;
 use Apiwpp\Api\Evolution\Message;

```

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Account;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;
  
 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');

```

## Criar instância

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Criar instância
 $create = Device::create("NOVO_TOKEN_123", "NOME_INSTANCIA");

 if($create){
    echo 'Instância criada com sucesso!';
 }else{
    var_dump(ExceptionError::getMessage()); // Json response
 }

```



## Recuperar Qrcode

É importante ressaltar que, em caso de buscar o qrcode com dispositivo já conectado retornará erro.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 $connected = Device::isConnected(); // false or true

 if(!$connected){
    // caso n esteja conectado carrega o qrcode
    Device::loadQr();
 
    // após carregar o qrcode podemos recupera-lo
    $qrcode = Device::getQrcode();

    // verifica se houve algum erro no processo
    if(ExceptionError::$error && $qrcode != "" && $qrcode != NULL){
        // se houve erro imprimir o erro
       var_dump(ExceptionError::getMessage()); // Json response
    }else{
        // Se não ocorreu erro mostra o qrcode
        echo '<img src="'.$qrcode.'" /> ';
    }

 }else{
    // Dispositivo já está conectado
    echo 'Conectado!';
 }


```


## Desconectar instância

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 $connected = Device::isConnected(); // false or true

 if($connected){

    $logout = Device::logout();

    if($logout){
        echo 'Desconectado!';
    }else{
       echo 'Erro ao desconectar';
    }

 }else{
    echo 'Você já está desconectado!';
 }


```



## Deletar instância

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 $connected = Device::isConnected(); // false or true

 if(!$connected){
   
    $delete = Device::delete();

    if($delete){
        echo 'Deletado!';
    }else{
       echo 'Erro ao deletar';
    }

 }else{
    echo 'Faça logout primeiro';
 }


```



## Definir um WebHook para o dispositivo

Irá receber REQUEST:POST toda vez que receber uma mensagem

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 $setWebhook = Device::setWebhook('http://site.com'); // use 'disabled' para desativar o webhook

 
 var_dump($setWebhook); // true or false
 

```


## Recuperar o WebHook

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 $webhook = Device::getWebhook();
 
 var_dump($webhook); // object: ['subscribe' => 'Message', 'webhook' => 'http://site.com' ]


```

## Enviar mensagem de texto

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 
 Message::type('text');
 Message::phone('551199999999');
 Message::text('Mensagem aqui');

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }



```

## Enviar video com URL do arquivo.

> É importante informar, que apenas videos no formato .mp4 e .3gpp são aceitos.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/', 'Evolution2');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 
 Message::type('video');
 Message::phone('551199999999');
 Message::fileUrl('http://umsitequalquer.com/arquivos/video.mp4');
 Message::caption('Um texto anexado ao video'); // Opcional

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }



```



## Enviar audio com URL do arquivo.


```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 
 Message::type('audio');
 Message::phone('551199999999');
 Message::fileUrl('http://umsitequalquer.com/arquivos/audio.ogg');

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }



```

## Enviar imagem com URL do arquivo.


```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 
 Message::type('image');
 Message::phone('551199999999');
 Message::fileUrl('http://umsitequalquer.com/arquivos/imagem.png');
 Message::caption('Um texto anexado a imagem'); // Opcional

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }


```



## Enviar documento com URL do arquivo.

> Arquivos no formato: zip, xls, pdf, txt, doc, xml, json, ppt, pptx já foram testados e funcionam. 

> Para demais tipos de arquivos não fora testado.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123', 'NOME_INSTANCIA');
 
 Message::type('document');
 Message::phone('551199999999');
 Message::fileUrl('http://umsitequalquer.com/arquivos/arquivo.pdf');

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }


```

## Checar se um número existe no whatsapp

> Essa função também corrige o número caso o mesmo use ou não o nono digito.

```php

<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá ser usado para verificar o número
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE', 'NOME_INSTANCIA');
 
 Account::checkPhone('551199999999');
 $isWhatsapp = Account::$isWhatsapp;

 if($isWhatsapp){
    echo 'Whatsapp válido: ' . Account::$phoneValid;
 }else{
    echo 'Whatsapp inválido';
 }

 

```


## Capturar Status 'recado' da conta do whatsapp


```php


<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá ser usado para verificar o número
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE', 'NOME_INSTANCIA');
 
 Account::detailsAccount('551199999999');

 echo Account::$accountName;
 echo '<br />';
 echo Account::$accountStatus;

```


## Capturar imagem do perfil

> A imagem do perfil não pode estar privada.

> Caso a imagem do perfil esteja apenas para meus contatos, o dispositivo que você passa em "setInstance" deve estar na agenda de contatos do usuário em questão.

```php


<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Api\Evolution2\Device;
 use Apiwpp\Api\Evolution2\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://evo.server/');
 Api::debug(true); // default true - Para não debugar não chame a função, ou passe false como parametro

 // Define qual instancia irá ser usado para verificar o número
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE', 'NOME_INSTANCIA');

 $profileImg = Account::getImageProfile('5511999999999');

 echo "<img src='{$profileImg}' /> "; 
 

```
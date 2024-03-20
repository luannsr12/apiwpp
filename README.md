## Biblioteca para usar Api Whatsapp em Golang

Api original: [asternic/wuzapi](https://github.com/asternic/wuzapi/blob/main/API.md)

> Essa biblioteca foi feita para ser usada na versão modificada da api mencionada acima. Ou sejam ela não é compativel com a versão "Api original".

> Caso deseje adquirir a API modificada entrar em contato: [luanalvesnsr@gmail.com](mailto:luanalvesnsr@gmail.com)


## Funções disponíveis

- Endio de video
- Envio de audio
- Enviod de documentos
- Envio de imagens
- Envio de texto
- Checagem do númro do whatsapp
- Imagem do perfil
- Detalhes do perfil


## Instalação composer

```bash
 composer require luannsr12/apiwpp
```


## Criar instância

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
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
 use Apiwpp\Api\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 
 // Setar o token
 Device::setInstance('NOVO_TOKEN_123');
 $connected = Device::isConnected(); // false ou true

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



## Definir um WebHook para o dispositivo

Irá receber REQUEST:POST toda vez que receber uma mensagem

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 

 Device::setInstance('NOVO_TOKEN_123_TESTE');
 $setWebhook = Device::setWebhook('http://site.com');
 
 var_dump($setWebhook); // true or false
 

```


## Recuperar o WebHook

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(); // default true - Para não debugar não chame a função, ou passe false como parametro
 

 Device::setInstance('NOVO_TOKEN_123_TESTE');
 $webhook = Device::getWebhook();
 
 var_dump($webhook); // object: ['subscribe' => 'Message', 'webhook' => 'http://site.com' ]


```

## Enviar mensagem de texto

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
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

## Enviar video com URL do aqruivo.

> É importante informar, que apenas videos no formato .mp4 e 3gpp são aceitos.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
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



## Enviar audio com URL do aqruivo.

> É importante informar, que apenas áudios no formato .ogg são aceitos.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
 Message::type('audio');
 Message::phone('551199999999');
 Message::fileUrl('http://umsitequalquer.com/arquivos/audio.ogg');
 Message::caption('Um texto anexado ao audio'); // Opcional

 if(Message::send()){
    echo 'Mensagem enviada! <br />';
    echo 'Id: ' . Message::$lastIdMessage;
 }else{
    var_dump(ExceptionError::getMessage());
 }



```

## Enviar imagem com URL do aqruivo.


```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
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



## Enviar documento com URL do aqruivo.

> Arquivos no formato: zip, xls, pdf, txt, doc, xml, json, ppt, pptx já foram testados e funcionam. 

> Para demais tipos de arquivos não fora testado.

```php
<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Error\ExceptionError;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Message;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá enviar a mensagem
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
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

```php

<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá ser usado para verificar o número
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
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
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá ser usado para verificar o recado
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE');
 
 Account::detailsAccount('551199999999');

 echo Account::$accountName;
 echo '<br />';
 echo Account::$accountStatus;

```


## Capturar imagem do perfil

> Se a imagem do perfil não pode estar privada.

> Caso a imagem do perfil esteja apenas para meus contatos, o dispositivo que você passa em "setInstance" deve estar na agenda de contatos do usuário em questão.

```php


<?php 
 
 require_once 'vendor/autoload.php';

 use Apiwpp\Config\Api;
 use Apiwpp\Api\Device;
 use Apiwpp\Api\Account;

 // Definir configurações da API
 Api::setConfigs('TOKEN_ADMIN', 'http://127.0.0.1/apiwpp/');
 Api::debug(true); // default true - Para não debugar não chame a função

 // Define qual instancia irá ser usado para verificar a imagem
 // Pode ser o token admin aqui também
 Device::setInstance('NOVO_TOKEN_123_TESTE');

 $profileImg = Account::getImageProfile('5511999999999');

 echo "<img src='{$profileImg}' /> "; 
 

```
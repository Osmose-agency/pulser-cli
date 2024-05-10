<?php
namespace Pulser\Tools;

class ConfigClient
{
    public $api_url = "https://pulser-v2.test";
    public $api_url_uri = "/api";


    // check if file exists
    public static function checkConfFile()
    {
        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        if(!file_exists("$configDir/config.json")){
            return false;
        }
        
        return true;
    }


    // check if token is valid
    public static function checkApiToken()
    {
        $apiToken = self::getConfig("apiToken");
        if(!$apiToken){
            return false;
        }
        

        $client = HttpClient::createForBaseUri($this->api_url, [
            'auth_bearer' => $apiToken,
        ]);

        $result = $client->request(
            'GET',
            'user',
            [
                'headers' => [
                    "Authorization" => 'Bearer ' . $apiToken
                ]
            ]
        );
        if($result->getStatusCode() == "401"){
            return false;
        }

        return true;
    }

    // check if OpenAI API key exists
    public static function checkOpenAIKey()
    {
        if(!self::getConfig("openAIToken")){
            return false;
        }

        return true;
    }

    public static function setConfig($key, $value){
        if(!self::checkConfFile()){
            return false;
        }

        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        if (!self::checkConfFile()) {
            mkdir($configDir, 0700, true);
            $config = [];
        }else{
            $configFile = file_get_contents("$configDir/config.json");
            $config = json_decode($configFile, true);
        }

        $config[$key] = $value;

        file_put_contents("$configDir/config.json", json_encode($config));
        return true;
    }

    //set ApiToken
    public static function setApiToken($token)
    {
        return self::setConfig('apiToken', $token);
    }

    // set OpenAIKey
    public static function setOpenAIKey($token)
    {
        return self::setConfig('openAIToken', $token);
    }

    // get ApiToken
    public static function getApiToken()
    {
        return self::getConfig('apiToken');
    }

    // get openAIToken
    public static function getOpenAIKey()
    {
        return self::getConfig('openAIToken');
    }

    public static function getConfig($key){
        if(!self::checkConfFile()){
            return false;
        }

        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        $configFile = file_get_contents("$configDir/config.json");
        $config = json_decode($configFile, true);
        if(empty($config[$key])){
            return false;
        }

        return $config[$key];
    }
}
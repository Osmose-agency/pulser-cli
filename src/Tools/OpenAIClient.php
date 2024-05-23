<?php
namespace Pulser\Tools;

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Chat\Enums\OpenAIChatModel;

class OpenAIClient
{

    public static function vision($prompts, $system = null, $options = [])
    {
        $api_key = ConfigClient::getConfig("openAIToken");
        if(!$api_key) throw new \Exception("OpenAI API key not configured.");
        
        $config = new OpenAIConfig();

        $config->apiKey = $api_key;
        $config->model = 'gpt-4o';
        $config->modelOptions = $options;
        $chat = new OpenAIChat($config);

        if($system) $chat->setSystemMessage($system);
        $generatedChat = $chat->generateChat([
            [
                "role" => "user",
                "content" => $prompts,
            ],
        ]);

        return $generatedChat;
    }

    public static function chat($promps, $system = null)
    {
        $api_key = ConfigClient::getConfig("openAIToken");
        if(!$api_key) throw new \Exception("OpenAI API key not configured.");

        $config = new OpenAIConfig();
        $config->apiKey = $api_key;
        $config->model = OpenAIChatModel::Gpt35Turbo->getModelName();
        $chat = new OpenAIChat($config);

        if($system) $chat->setSystemMessage($system);
        $generatedChat = $chat->generateChat([
            [
                "role" => "user",
                "content" => [
                    $prompts
                ],
            ],
        ]);

        return $generatedChat;
    }
}
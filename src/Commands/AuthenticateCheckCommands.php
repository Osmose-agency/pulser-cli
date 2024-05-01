<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\HttpClient\HttpClient;

class AuthenticateCheckCommands extends Command
{

    protected function configure(): void
    {
        $this
            ->setName('auth:check')
            ->setDescription('Check API token presence');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get argument value
        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        if(!file_exists("$configDir/config.json")){
            $output->writeln('API token not configured.');
            return Command::FAILURE;
        }
        $tokenFile = file_get_contents("$configDir/config.json");
        $token = json_decode($tokenFile, true);
        if(empty($token['apiToken'])){
            $output->writeln('API token not configured.');
            return Command::FAILURE;
        }

        $client = HttpClient::createForBaseUri('https://pulser-v2.test/', [
            'auth_bearer' => $token['apiToken'],
        ]);
        

        $result = $client->request(
            'GET',
            'https://pulser-v2.test/api/user',
            [
                'headers' => [
                    "Authorization" => 'Bearer ' . $token['apiToken']
                ]
            ]
        );
        if($result->getStatusCode() == "401"){
            $output->writeln('Unauthorized. Please check your API token.');
            return Command::FAILURE;
        }

        $output->writeln("Hi " . json_decode($result->getContent(), true)['name'] . "! Your API token is valid.");
        return Command::SUCCESS;
    }
}
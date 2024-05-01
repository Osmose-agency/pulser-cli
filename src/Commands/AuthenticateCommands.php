<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AuthenticateCommands extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('auth:token')
            ->setDescription('Configures the API token')
            ->addArgument('token', InputArgument::REQUIRED, 'Your personnal API token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get argument value
        $token = $input->getArgument('token');
        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        if (!file_exists($configDir)) {
            mkdir($configDir, 0700, true);
        }

        file_put_contents("$configDir/config.json", json_encode(['apiToken' => $token]));
        $output->writeln('API token configured successfully.');
        return Command::SUCCESS;
    }
}
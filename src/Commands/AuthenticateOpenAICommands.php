<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Pulser\Tools\ConfigClient;

class AuthenticateOpenAICommands extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('auth:openai')
            ->setDescription('Configures the OpenAI API token')
            ->addArgument('token', InputArgument::REQUIRED, 'Your personnal API token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get argument value
        $token = $input->getArgument('token');

        if(!ConfigClient::setOpenAIKey($token)){
            $output->writeln('API token not configured.');
            return Command::FAILURE;
        }

        $output->writeln('API token configured successfully.');
        return Command::SUCCESS;
    }
}
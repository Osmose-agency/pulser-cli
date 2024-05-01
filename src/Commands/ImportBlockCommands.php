<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpClient\HttpClient;

class ImportBlockCommands extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('import:block')
            ->setDescription('Import a new block from Pulser Block Library')
            ->setHelp('This command allows you to import a new block from Pulser Block Library...')
            ->addArgument('repo', InputArgument::REQUIRED, 'The repository name of the block')
            ->addOption('nophp', null, InputOption::VALUE_NONE, 'Do not import php file content')
            ->addOption('noacf', null, InputOption::VALUE_NONE, 'Do not import acf configuration file content')
            ->addOption('nocss', null, InputOption::VALUE_NONE, 'Do not import css file content');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $input->getArgument('repo');

        $configDir = $_SERVER['HOME'] . '/.pulser-cli';
        if(!file_exists("$configDir/config.json")){
            $io->error('API token not configured.');
            return Command::FAILURE;
        }
        $tokenFile = file_get_contents("$configDir/config.json");
        $token = json_decode($tokenFile, true);
        if(empty($token['apiToken'])){
            $io->error('API token not configured.');
            return Command::FAILURE;
        }

        $client = HttpClient::createForBaseUri('https://pulser-v2.test/', [
            'auth_bearer' => $token['apiToken'],
        ]);

        $result = $client->request(
            'GET',
            'https://pulser-v2.test/api/block/' . $repo,
            [
                'headers' => [
                    "Authorization" => 'Bearer ' . $token['apiToken']
                ]
            ]
        );

        if($result->getStatusCode() == "401"){
            $io->error('Unauthorized. Please check your API token.');
            return Command::FAILURE;
        }

        
        if($result->getStatusCode() == "404"){
            $io->error('Not found... Please check the block name and try again.');
            return Command::FAILURE;
        }


        // create temporary zip file from response content
        $zipFile = tempnam(sys_get_temp_dir(), 'pulser-block-');
        file_put_contents($zipFile, $result->getContent());

        // extract zip file into blocks directory
        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $name = $zip->getNameIndex(0);
            $name = rtrim($name,"/");

            $zip->extractTo('blocks/');
            $zip->close();
            unlink($zipFile);

            
            $extractedDir = "blocks/$name";
            rename($extractedDir, 'blocks/' . $repo);
            //check options and empty files
            if($input->getOption('nophp')){
                // empty the php file
                $phpFile = 'blocks/' . $repo . '/block.php';
                file_put_contents($phpFile, '<?php'."\r\n".'// Silence is golden.');
            }
            if($input->getOption('nocss')){
                // empty the php file
                $cssFile = 'blocks/' . $repo . '/block.css';
                file_put_contents($cssFile, '');
            }
            if($input->getOption('noacf')){
                // empty the php file
                $acfFile = 'blocks/' . $repo . '/acf/fields.json';
                if(file_exists($acfFile)){
                    // read the file and empty the fields key
                    $acfContent = file_get_contents($acfFile);
                    $acfContent = json_decode($acfContent, true);
                    $acfContent['fields'] = [];
                    file_put_contents($acfFile, json_encode($acfContent, JSON_PRETTY_PRINT));
                }
            }

            $io->success("Block $repo imported successfully.");
        } else {
            $io->error("Failed to import block $repo.");
        }

        return Command::SUCCESS;
    }
}


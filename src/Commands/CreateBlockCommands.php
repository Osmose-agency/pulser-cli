<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

use Pulser\Tools\OpenAIClient;

class CreateBlockCommands extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('create:block')
            ->setDescription('Create a new block')
            ->setHelp('This command allows you to create a new empty block...')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the block')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'The title of the block')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'The description of the block')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'The category of the block')
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'The icon of the block')
            ->addOption('image', null, InputOption::VALUE_OPTIONAL, 'The image of the block');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $acf_json = null;
        $php_tpl = null;

        $progressBar = new ProgressBar($output, 3);
        $progressBar->start();
        // get argument value
        $name = $input->getArgument('name');
        //sanitize name
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);

        // get options values
        $options = $input->getOptions();
        $progressBar->advance();
        if(isset($options["image"])){
            // check if image exists
            if(!file_exists($options["image"])){
                $output->writeln('Image "'.$options["image"].'" not found!');
                return Command::FAILURE;
            }
            
            $path = $options["image"];
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            
            require __DIR__ . '/../Configs/prompts.php';

            $configPrompts = config([
                "acf_json" => "",
                "name" => $name,
                "base64" => $base64,
            ]);

            $systemACF = $configPrompts["acf_from_image"]["system"];
            $promptsACF = $configPrompts["acf_from_image"]["prompts"];
            
            $acf_json = OpenAIClient::vision($promptsACF, $systemACF, [
                "response_format" => [ "type"=> "json_object" ],
            ]);
            $acf_array = json_decode($acf_json, true);
            if(!isset($acf_array["fields"])){
                $output->writeln('Error creating ACF fields from image!');
                return Command::FAILURE;
            }
            $acf_fields = $acf_array["fields"];

            $progressBar->advance();
            $configPrompts = config([
                "acf_json" => $acf_json,
                "name" => "",
                "base64" => $base64,
            ]);
            
            $systemPHP = $configPrompts["php_from_acfimage"]["system"];
            $promptsPHP = $configPrompts["php_from_acfimage"]["prompts"];

            $php_tpl = OpenAIClient::vision($promptsPHP, $systemPHP);
            $progressBar->advance();
        }

        // check if block already exists
        if(file_exists('blocks/'.$name)){
            $output->writeln('Block "'.$name.'" already exists!');
            return Command::FAILURE;
        }
        // create block
        $output->writeln('Creating new block "'.$name.'"...');
        $currentDirectory = getcwd();
        $blockDirectory = $currentDirectory.'/blocks/'.$name;
        if(!file_exists($currentDirectory.'/blocks/')){
            mkdir($currentDirectory.'/blocks/');
        }
        if (!file_exists($blockDirectory)) {
            mkdir($blockDirectory);
        }

        mkdir("blocks/$name/acf");
        file_put_contents("blocks/$name/acf/fields.json", json_encode($this->getDefaultAcfJson($options, $name, $acf_fields), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($currentDirectory."/blocks/".$name."/block.json", json_encode($this->getDefaultBlockJson($options, $name), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($currentDirectory."/blocks/".$name."/block.php", $this->getDefaultBlockTemplate($options, $name, $php_tpl));
        file_put_contents($currentDirectory."/blocks/".$name."/block.css", $this->getDefaultBlockCSS($options, $name));
        

        $progressBar->finish();
        $output->writeln('Block "'.$name.'"created!');
        return Command::SUCCESS;
    }

    protected function getDefaultBlockJson($options, $name = "block"){
        return  [
            "name" => "pulser/" . $name, 
            "title" => $options["title"] ?? $name, 
            "description" =>  $options["description"] ?? "", 
            "style" => [
                "file:./block.css" 
            ],
            "category" => $options["category"] ?? "pulser", 
            "icon" => $options["icon"] ?? "admin-generic", 
            "keywords" => [
                $name, 
                "pulser" 
            ], 
            "acf" => [
                "mode" => "auto", 
                "renderTemplate" => "block.php" 
            ], 
            "supports" => [
                "anchor" => true 
            ] 
        ];
    }

    protected function getDefaultAcfJson($options, $name = "block", $fields = []){
        return [
                "key"=> "group_".uniqid(),
                "title"=> "Block - ".($options["title"] ?? $name),
                "fields"=> $fields,
                "location"=> [
                    [
                        [
                            "param"=> "block",
                            "operator"=> "==",
                            "value"=> "pulser/".$name
                        ]
                    ]
                ],
                "menu_order"=> 0,
                "position"=> "normal",
                "style"=> "default",
                "label_placement"=> "top",
                "instruction_placement"=> "label",
                "hide_on_screen"=> "",
                "active"=> true,
                "description"=> "",
                "show_in_rest"=> 0
        ];
    }

    protected function getDefaultBlockCss($options, $name = "block"){
        return "/*\n".$name." Block Styles\n */\n";
    }

    protected function getDefaultBlockTemplate($options, $name = "block", $tpl = ""){
        return "<?php\n/*\n".$name." Block Template\n */\n?>".$tpl;
    }

}


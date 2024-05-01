<?php
namespace Pulser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'The icon of the block');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get argument value
        $name = $input->getArgument('name');
        //sanitize name
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);

        // get options values
        $options = $input->getOptions();

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

        file_put_contents($currentDirectory."/blocks/".$name."/block.json", json_encode($this->getDefaultBlockJson($options, $name), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($currentDirectory."/blocks/".$name."/block.php", $this->getDefaultBlockTemplate($options, $name));
        file_put_contents($currentDirectory."/blocks/".$name."/block.css", $this->getDefaultBlockCSS($options, $name));
        
        mkdir("blocks/$name/acf");
        file_put_contents("blocks/$name/acf/fields.json", json_encode($this->getDefaultAcfJson($options, $name), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


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

    protected function getDefaultAcfJson($options, $name = "block"){
        return [
                "key"=> "group_".uniqid(),
                "title"=> "Block - ".($options["title"] ?? $name),
                "fields"=> [
                    
                ],
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

    protected function getDefaultBlockTemplate($options, $name = "block"){
        return "<?php\n/*\n".$name." Block Template\n */\n?>";
    }

}


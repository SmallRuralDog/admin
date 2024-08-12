<?php

namespace smallruraldog\admin\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssetsCommand extends Command
{
    protected static string $defaultName = 'smallruraldog-admin:assets';
    protected static string $defaultDescription = '发布静态资源';


    protected static $pathRelation = array(
        __DIR__.'/../../web/dist/assets' => 'public/admin/assets',
        __DIR__.'/../../web/dist/amis' => 'public/admin/amis',
    );

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            copy_dir($source, base_path() . "/$dest");
            $output->writeln("复制 $dest");
        }


        return self::SUCCESS;
    }
}
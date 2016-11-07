<?php
/*
  * This file is part of the TMS Sync package.
 *
 * (c) Matthias Vandermaesen <matthias@colada.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Flemishartcollection\TMSSync;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Exporter extends Command {
    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('tmssync:export')
            ->setDescription('Export data from TMS to MySQL');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        var_dump('test');
    }
}

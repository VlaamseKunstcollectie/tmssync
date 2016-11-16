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
use Flemishartcollection\TMSSync\Database\Destination;
use Flemishartcollection\TMSSync\Database\Source;

class Exporter extends Command {

    private $destination;

    private $source;

    public function setDestination(Destination $destination) {
        $this->destination = $destination;
    }

    public function setSource(Source $source) {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('tmssync:export')
            ->setDescription('Export data from TMS to MySQL');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $formatter = $this->getHelper('formatter');
        $formattedLine = $formatter->formatSection(
            'SomeSection',
            'Here is some message related to that section'
        );
        $output->writeln($formattedLine);

        $errorMessages = array('Error!', 'Something went wrong');
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);

        // Truncate all Destination database tables.
        $this->destination->truncate('users');

        // Fetch data from Source database tables

        // Write Source database tables to temp CSV files

        // Read out CSV files and store in Destination database tables

        // Remove temp CSV files

    }
}

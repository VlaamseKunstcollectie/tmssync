<?php
/**
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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Flemishartcollection\TMSSync\Database\Destination;
use Flemishartcollection\TMSSync\Database\Source;

/**
 * Exporter.
 *
 * This class implements the tms:export class. This class fetches data from a
 * "Source", stores it in a set of intermediate CSV files and then imports those
 * files to a "Destination".
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Exporter extends Command {
    /**
     * The destination to which data will be dumped.
     */
    private $destination;

    /**
     * The source from which data is fetched.
     */
    private $source;

    /**
     * Dependency method injection. Sets the destination class.
     *
     * @param Flemishartcollection\TMSSync\Database\Destination $destination The destination.
     */
    public function setDestination(Destination $destination) {
        $this->destination = $destination;
    }

    /**
     * Dependency method injection. Sets the destination class.
     *
     * @param Flemishartcollection\TMSSync\Database\Source $source The source.
     */
    public function setSource(Source $source) {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('tmssync:export')
            ->setDescription('Export data from TMS to MySQL')
            ->addOption(
                'fetch',
                'fe',
                InputOption::VALUE_OPTIONAL,
                'Fetch data from TMS source?',
                true
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if ($input->getOption('fetch')) {
            // Truncate all Destination database tables.
            $this->destination->truncate();

            // Fetch data from Source database tables and write to temp CSV files
            $this->source->fetch();
        }

        // Read out CSV files and store in Destination database tables
        $this->destination->dump();
    }
}

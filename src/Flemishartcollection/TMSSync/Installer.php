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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Flemishartcollection\TMSSync\Database\Destination;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Installer.
 *
 * This class implements the tms:install command. This command installs the
 * database schema defined in app/config/schema.yml to "Destination".
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Installer extends Command {
    /**
     * The destination to which data will be dumped.
     */
    private $destination;

    /**
     * Dependency method injection. Sets the destination class.
     *
     * @param Flemishartcollection\TMSSync\Database\Destination $destination The destination.
     */
    public function setDestination(Destination $destination) {
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName('tmssync:install')
            ->setDescription('Installs the MySQL database schema.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $logger = new Logger('console');

        $verbosityLevelMap = array(
            OutputInterface::VERBOSITY_NORMAL => Logger::ERROR,
            OutputInterface::VERBOSITY_NORMAL => Logger::WARNING,
            OutputInterface::VERBOSITY_NORMAL => Logger::NOTICE,
            OutputInterface::VERBOSITY_NORMAL => Logger::INFO,
            OutputInterface::VERBOSITY_NORMAL => Logger::DEBUG,
        );

        $logger->pushHandler(new ConsoleHandler($output, true, $verbosityLevelMap));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        $this->destination->setLogger($logger);

        $this->destination->createSchema();
    }
}

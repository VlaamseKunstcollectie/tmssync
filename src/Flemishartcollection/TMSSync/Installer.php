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
use Flemishartcollection\TMSSync\Database\Connection;
use Doctrine\DBAL\Schema\Schema;

class Installer extends Command {
    private $connection;

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection();
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
        $schema = new Schema();

        /* Now use the Schema object to create a 'users' table */
        $usersTable = $schema->createTable("users");
        /* Add some columns to the table */
        $usersTable->addColumn("id", "integer", array("unsigned" => true));
        $usersTable->addColumn("first_name", "string", array("length" => 64));
        $usersTable->addColumn("last_name", "string", array("length" => 64));
        $usersTable->addColumn("email", "string", array("length" => 256));
        $usersTable->addColumn("website", "string", array("length" => 256));
        /* Add a primary key */
        $usersTable->setPrimaryKey(array("id"));

        $platform = $this->connection->getDatabasePlatform();
        $queries = $schema->toSql($platform);

        foreach ($queries as $query) {
            $this->connection->query($query);
        }
    }
}

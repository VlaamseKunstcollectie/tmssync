<?php
/*
  * This file is part of the TMS Sync package.
 *
 * (c) Matthias Vandermaesen <matthias@colada.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Flemishartcollection\TMSSync\Database;

use Exception;
use Doctrine\DBAL\Schema\Schema;
use Flemishartcollection\TMSSync\Database\Connection;
use Flemishartcollection\TMSSync\Database\DatabaseInterface;

class Destination implements DatabaseInterface {

    private $connection;

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mysql');
    }

    public function insert() {
        // Insert a new line into the database table!!
    }

    /**
     * Truncate an entire table
     */
    public function truncate($tableName) {
        $this->connection->beginTransaction();
        try {
            $this->connection->query('SET FOREIGN_KEY_CHECKS=0');
            $this->connection->query(sprintf('DELETE FROM %s', $tableName));
            $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e; // bubble up to command
        }

        $this->connection->beginTransaction();
        try {
            $this->connection->query(sprintf('ALTER TABLE %s AUTO_INCREMENT = 1', $tableName));
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e; // bubble up to command
        }

        return true;
    }

    /**
     * Create the schema for Destination
     */
    public function createSchema() {
        $schema = new Schema();

        /* Now use the Schema object to create a 'users' table */
        $objectsTable = $schema->createTable("objects");

        // time created
        // last time updated (TBD)
        // MD5 of record (was record updated of late?) (TBD)
        // id = autoincrement (internal id)
        // TMS :: database id
        // TMS :: ...

        $objectsTable->addColumn("id", "integer", array("unsigned" => true));
        $objectsTable->addColumn("first_name", "string", array("length" => 64));
        $objectsTable->addColumn("last_name", "string", array("length" => 64));
        $objectsTable->addColumn("email", "string", array("length" => 256));
        $objectsTable->addColumn("website", "string", array("length" => 256));

        $objectsTable->setPrimaryKey(array("id"));

        $platform = $this->connection->getDatabasePlatform();
        $queries = $schema->toSql($platform);

        foreach ($queries as $query) {
            $this->connection->query($query);
        }
    }
}

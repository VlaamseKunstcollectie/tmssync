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
use Flemishartcollection\TMSSync\Configuration\Configuration as Parameters;
use Flemishartcollection\TMSSync\Filesystem\CSVReader;

class Destination implements DatabaseInterface {

    private $connection;

    private $parameters;

    public function __construct(Parameters $parameters) {
        $this->parameters = $parameters->process();
    }

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mysql');
    }

    /**
     * Dump tables from CSV to MySQL destination
     */
    public function dump() {
        $mappings = $this->parameters['mapping'];
        $tables = $this->parameters['tables'];

        foreach ($mappings as $mapping) {
            $destination = $mapping['destination'];

            if (isset($tables[$destination])) {
                // Fetch columns from config
                $columns = array_map(function ($props) {
                    return $props['name'];
                }, $tables[$destination]['columns']);
                $cols = implode(',', $columns);

                // Set placeholders values
                $placeholders = array_map(function ($props) {
                    return ':' . $props['name'];
                }, $tables[$destination]['columns']);
                $values = implode(',', $placeholders);

                $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $destination, $cols, $values);

                // Open up the associated CSV file for the selected destination
                $csv = new CSVReader();
                $reader = $csv->get($destination);
                $reader->setOffset(1);
                $results = $reader->fetch();

                // Read out each row and store it into the databse
                foreach ($results as $row) {
                    try {
                        $this->connection->beginTransaction();
                        $sth = $this->connection->prepare($sql);
                        $placeholders = array_values($placeholders);
                        foreach ($row as $key => $value) {
                            $sth->bindValue($placeholders[$key], $value);
                        }
                        $sth->execute();
                        $this->connection->commit();
                    } catch (Exception $e) {
                        $this->connection->rollback();
                        throw $e; // bubble up to command
                    }
                }
            }
        }
    }

    /**
     * Truncate an entire table
     */
    public function truncate() {
        $tables = array_keys($this->parameters['tables']);
        foreach ($tables as $tableName) {
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
        }

        return true;
    }

    /**
     * Create the schema for Destination
     */
    public function createSchema() {
        $schema = new Schema();

        $tables = $this->parameters['tables'];

        foreach($tables as $tableName => $props) {
            $table = $schema->createTable($tableName);

            foreach ($props['columns'] as $key => $colProps) {
                $attributes = array_filter($colProps['attributes'][0]);
                $table->addColumn($colProps['name'], $colProps['type'], $attributes);
            }
            $table->setPrimaryKey(array($props['primaryKey']));
        }

        $platform = $this->connection->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        foreach ($queries as $query) {
            $this->connection->query($query);
        }
    }
}

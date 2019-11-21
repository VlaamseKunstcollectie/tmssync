<?php
/**
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
use Monolog\Logger;

/**
 * Destination class
 *
 * Represents the "Destination" to which the data will be mirrored. This class
 * is an API for the application\console commands. It contains all the core
 * logic needed to interact with a MySQL database.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Destination implements DatabaseInterface {
    /**
     * An instance representing the database connection.
     */
    private $connection;

    /**
     * Array of processed configuration variables.
     */
    private $parameters;

    private $logger;

    /**
     * Constructor
     *
     * @param Flemishartcollection\TMSSync\Configuration\Configuration App specific parameters
     */
    public function __construct(Parameters $parameters) {
        $this->parameters = $parameters->process();
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mysql');
    }

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Dump tables from CSV to MySQL destination.
     *
     * Takes the list configured in the "mapping" key and dumps data to the
     * destination database accordingly. This function will first fetch the
     * associated CSV file, load the data and dump it to a corresponding MySQL
     * table. Notes:
     *
     *  - Dumps whatever is defined in the "mapping" key to the database. You
     *    can define multiple tables to be dumped.
     *  - The database schema stored in app\config\schema.yml was used to create
     *    the required database structure. If the corresponding table does not
     *    exists, a SQL error is thrown.
     *  - The "mapping" key can *only* contain destination tables which are
     *    defined in the schema.
     *  - The table structure in schema has to mirror the structure of the CSV
     *    file. Mismatches will result in a SQL error upon inserting data.
     */
    public function dump($exclusive = array()) {
        $mappings = $this->parameters['mapping'];
        $tables = $this->parameters['tables'];

        $this->logger->info('**** DUMPING DATA TO DESTINATION DATABASE');

        foreach ($mappings as $mapping) {
            $destination = $mapping['destination'];

            // Skip table if in exclusive mode
            if (!empty($exclusive)) {
                if (!in_array($destination, $exclusive)) {
                    $this->logger->warning('Skipping table {destination}', ['destination' => $destination]);
                    continue;
                }
            }

            $this->logger->info('Dumping to {destination}', ['destination' => $destination]);

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
                $results = $reader->fetchAssoc();

                // Read out each row and store it into the databse
                foreach ($results as $id => $row) {
                    try {
                        $this->connection->beginTransaction();
                        $sth = $this->connection->prepare($sql);

                        $row['tmsid'] = $id; // acount for the autoincrement id

                        foreach ($row as $key => $value) {
                            if (in_array($key, array_keys($placeholders))) {
                                $sth->bindValue($placeholders[$key], $value);
                            }
                        }

                        $sth->execute();
                        $this->connection->commit();
                    } catch (Exception $e) {
                        $this->connection->rollback();
                        $this->logger->error($e->getMessage());
                    }
                }

                $this->logger->info('DONE dumping to {destination}', ['destination' => $destination]);
            }
        }
    }

    /**
     * Truncate an entire database.
     *
     * All tables defined in app\config\schema.yml will be truncated through
     * this function. Will also reset the auto_increment if set.
     *
     * @throws Exception Exception if the truncation fails for a particular
     *   table
     * @return boolean True if truncate was succesful.
     */
    public function truncate($exclusive) {
        $tables = array_keys($this->parameters['tables']);
        foreach ($tables as $tableName) {

            // Skip table if in exclusive mode
            if (!empty($exclusive)) {
                if (!in_array($tableName, $exclusive)) {
                    $this->logger->warning('Skipping table {tableName}', ['tableName' => $tableName]);
                    continue;
                }
            }

            $this->connection->beginTransaction();
            try {
                $this->connection->query('SET FOREIGN_KEY_CHECKS=0');
                $this->connection->query(sprintf('DELETE FROM %s', $tableName));
                $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
                $this->connection->commit();
                $this->logger->info('table {table} has been truncated', ['table' => $tableName]);
            } catch (Exception $e) {
                $this->connection->rollback();
                $this->logger->error($e->getMessage());
            }

            // @todo
            //  reset increment only if autoincrement is true for this table.

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
     * Create the schema for Destination.
     *
     * Reads out the configuration in app\config\schema.yml and creates tables
     * based on the configuration.
     *
     * @todo Support database updates!!
     */
    public function createSchema() {
        $schema = new Schema();

        $tables = $this->parameters['tables'];

        foreach($tables as $tableName => $props) {
            $table = $schema->createTable($tableName);

            foreach ($props['columns'] as $key => $colProps) {
                $attributes = array();
                if (isset($colProps['attributes'])) {
                    if (isset($colProps['attributes'][0])) {
                        $attributes = array_filter($colProps['attributes'][0]);
                    }
                }

                $attributes["notnull"] = false;

                $table->addColumn($colProps['name'], $colProps['type'], $attributes);
            }
            $table->setPrimaryKey(array($props['primaryKey']));

            $this->logger->info('Prepared {table}', ['table' => $tableName]);
        }

        $platform = $this->connection->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        foreach ($queries as $query) {
            try {
                $this->connection->query($query);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->logger->info('Done!');
    }
}

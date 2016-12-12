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
use Flemishartcollection\TMSSync\Filesystem\CSVWriter;
use Monolog\Logger;

/**
 * Source class
 *
 * Represents the "Source" to which the data will be mirrored. This class
 * is an API for the application\console commands. It contains all the core
 * logic needed to interact with a Microsoft SQL database.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Source implements DatabaseInterface {
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
        $this->connection = $connection;
    }

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Fetch data from source and store it in CSV files.
     *
     * Each table defined in the "mapping" key as "source" will be queried.
     * For each table, an associated CSV file with "destination" will be
     * created. Each row will be stored in this file. Notes:
     *
     * - Only tables defined as "source" in the "mapping" key will be dumped.
     * - ...
     *
     * @throws Exception An exception if something goes wrong.
     * @return boolean true when the operation was completed succesfully.
     */
    public function fetch($exclusive = array()) {
        $mapping = $this->parameters['mapping'];
        $tables = $this->parameters['tables'];

        $this->logger->info('**** DUMPING DATA TO SOURCE');

        // Group the mappings by 'db' key.
        $databases = array();
        foreach ($mapping as $key => $val) {
            $databases[$val['db']][] = $val;
        }

        // Loop over each database.
        foreach ($databases as $db => $tableMapping) {
            $connection = $this->connection->getConnection($db);

            $this->logger->info('Fetching from {database}', ['database' => $db]);

            // Loop over each table mapping
            foreach ($tableMapping as $mapping) {
                $destination = $mapping['destination'];
                $source = $mapping['source'];

                // Skip table if in exclusive mode
                if (!empty($exclusive)) {
                    if (!in_array($destination, $exclusive)) {
                        $this->logger->warning('Skipping table {source}', ['source' => $source]);
                        continue;
                    }
                }

                $this->logger->info('Fetching from table {source}', ['source' => $source]);

                // Get all data from this table
                $sql = sprintf("SELECT * FROM %s", $source);
                $rows = $connection->query($sql)->fetchAll();

                // Get the header
                $header = array_keys($rows[0]);

                if (isset($tables[$destination])) {
                    $csv = new CSVWriter();
                    $csv->createCSV($destination);
                    $csv->setHeader($header);

                    foreach ($rows as $row) {
                        $csv->insertONe($row);
                    }

                    // SELECT and FETCH from the database. Store it to CSV if any.
                    // MAPPING HAPPENS HERE!!!
                }

                $this->logger->info('DONE fetching from table {source}', ['source' => $source]);
            }
        }
    }
}

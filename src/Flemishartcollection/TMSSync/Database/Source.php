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
use League\Csv\Writer;
use Flemishartcollection\TMSSync\Database\Connection;
use Flemishartcollection\TMSSync\Database\DatabaseInterface;
use Flemishartcollection\TMSSync\Configuration\Configuration as Parameters;
use Flemishartcollection\TMSSync\Filesystem\CSV;

class Source implements DatabaseInterface {

    private $connection;

    private $parameters;

    public function __construct(Parameters $parameters) {
        $this->parameters = $parameters->process();
    }

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mssql');
    }

    public function fetch() {
        $mappings = $this->parameters['mapping'];
        $tables = $this->parameters['tables'];

        foreach ($mappings as $mapping) {
            $destination = $mapping['destination'];
            if (isset($tables[$destination])) {
                $header = array_map(function ($props) {
                    return $props['name'];
                }, $tables[$destination]['columns']);

                $csv = new CSV();
                $csv->createCSV($destination);
                $csv->setHeader($header);
            }
        }
    }
}

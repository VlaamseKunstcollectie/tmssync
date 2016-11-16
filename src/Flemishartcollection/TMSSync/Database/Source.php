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

class Source implements DatabaseInterface {

    private $connection;

    private $parameters;

    public function __construct(Parameters $parameters) {
        $this->parameters = $parameters->process();
    }

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mssql');
    }

    public function fetch($tableName) {
        // Parameters => mapping!!

        // Fetch all records from the entire table and write them to a CSV
        // file
    }
}

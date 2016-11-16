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

class Source implements DatabaseInterface {

    private $connection;

    public function setConnection(Connection $connection) {
        $this->connection = $connection->getConnection('mssql');
    }

    public function fetch($tableName) {
        // Fetch all records from the entire table and write them to a CSV
        // file
    }
}

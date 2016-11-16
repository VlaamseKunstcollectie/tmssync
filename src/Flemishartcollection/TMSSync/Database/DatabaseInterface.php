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

use Flemishartcollection\TMSSync\Database\Connection;

/**
 * The database interface for database classes.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
interface DatabaseInterface {
    /**
     * Inject a loaded Connection instance.
     *
     * @param Flemishartcollection\TMSSync\Database\Connection The loaded Connection instance.
     */
    public function setConnection(Connection $connection);
}

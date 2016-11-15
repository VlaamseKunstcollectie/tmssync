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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Flemishartcollection\TMSSync\Configuration\Configuration as Parameters;

class Connection {
    private $configuration;

    private $parameters;

    public function __construct(Parameters $parameters) {
        $this->configuration = new Configuration();
        $this->parameters = $parameters->process();
    }

    public function getConnection($type = 'mysql') {
        $connectionParams = $this->parameters[$type];
        return DriverManager::getConnection($connectionParams, $this->configuration);
    }
}

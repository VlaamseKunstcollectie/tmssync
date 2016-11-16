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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Flemishartcollection\TMSSync\Configuration\Configuration as Parameters;

/**
 * Connection wrapper.
 *
 * A wrapper around Doctrine\DBAL. Loads and process the configuration through
 * Symfony\Config and instantiates a new Connection object.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Connection {
    /**
     * Instance of Doctrine\DBAL\Configuration
     */
    private $configuration;

    /**
     * Array of processed configuration variables.
     */
    private $parameters;

    /**
     * Constructor
     *
     * @param Flemishartcollection\TMSSync\Configuration\Configuration App specific parameters
     */
    public function __construct(Parameters $parameters) {
        $this->configuration = new Configuration();
        $this->parameters = $parameters->process();
    }

    /**
     * Get a database connection
     *
     * Create a connection to a (remote) database server through Doctrine\DBAL.
     * Either instantiate a Microsoft SQL server or a MySQL server connection.
     * The resulting object will be used throughout the application to interact
     * with the databases. Settings are pulled from app/config/config.yml.
     *
     * @param string Either 'mysql' or 'mssql'
     *
     * @return Doctrine\DBAL\Connection A loaded Connection object.
     */
    public function getConnection($type = 'mysql') {
        $connectionParams = $this->parameters[$type];
        return DriverManager::getConnection($connectionParams, $this->configuration);
    }
}

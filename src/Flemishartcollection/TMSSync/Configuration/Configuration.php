<?php
/**
 * This file is part of the TMS Sync package.
 *
 * (c) Matthias Vandermaesen <matthias@colada.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Flemishartcollection\TMSSync\Configuration;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Flemishartcollection\TMSSync\Configuration\DatabaseConfiguration;
use Flemishartcollection\TMSSync\Configuration\SchemaConfiguration;

/**
 * Configuration manager for Symfony\Config.
 *
 * This class wires the Symfony\Config component in the app. It loads config
 * files stored in app/config, validates them and constructs an array of params
 * which are used throughout the app.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Configuration {
    /**
     * Instance of Symfony\Component\Config\Definition\Processor
     */
    private $processor;

    /**
     * The database configuration validator class
     */
    private $databaseConfiguration;

    /**
     * The database schema configuration validator class
     */
    private $schemaConfiguration;

    /**
     * Constructor
     *
     * @param Symfony\Component\Config\Definition\Processor $processor The processor
     */
    public function __construct(Processor $processor) {
        $this->processor = $processor;
        $this->databaseConfiguration = new DatabaseConfiguration();
        $this->schemaConfiguration = new SchemaConfiguration();
    }

    /**
     * Process the global configuration
     *
     * Process the YAML configuration files in app/config. Load them, validate
     * them and process them through the Symfony\Config component API. Returns
     * a validated array which is used through the application.
     *
     * @return array
     */
    public function process() {
        $configuration = [];

        // Lookup table for essential config files.
        $files = [
            'config.yml' => 'databaseConfiguration',
            'schema.yml' => 'schemaConfiguration'
        ];

        foreach ($files as $file => $processorClass) {
            // Fetch each YAML file
            $basepath = __DIR__ .'/../../../../app/config';
            $filePath = $basepath . '/' . $file;
            $contents = @file_get_contents($filePath);
            if (!$contents) {
                $msg = sprintf("ERROR: Are you sure the configuration file '%s' exists?", $filePath);
                exit($msg);
            }
            $YMLConfiguration = Yaml::parse($contents);

            // Process a yaml file and add to the global configuration array
            $configuration += $this->processor->processConfiguration(
                $this->{$processorClass},
                $YMLConfiguration
            );
        }

        return $configuration;
    }
}

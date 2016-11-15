<?php
/*
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

class Configuration {
    private $processor;

    private $databaseConfiguration;

    public function __construct(Processor $processor, DatabaseConfiguration $databaseConfiguration) {
        $this->processor = $processor;
        $this->databaseConfiguration = $databaseConfiguration;
    }

    public function process() {
        $configuration = null;

        try {
            $basepath = __DIR__ .'/../../../../app/config';
            $contents = file_get_contents($basepath . '/config.yml');
            $configuration = Yaml::parse($contents);
        } catch (\InvalidArgumentException $exception) {
            exit("Are you sure the configuration files exist?");
        }

        // Process the configuration files (merge one-or-more *.yml files)
        $configuration = $this->processor->processConfiguration(
            $this->databaseConfiguration,
            $configuration
        );

        return $configuration;
 /// TO BE CONTIONUED http://www.andrew-kirkpatrick.com/2014/08/example-use-symfony-config-component-standalone/
    }
}

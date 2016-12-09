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

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Database configuration class for Symfony\Config.
 *
 * Contains the validation rules for app/config/config.yml.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class DatabaseConfiguration implements ConfigurationInterface {

    /**
     *{@inheritdoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('params');
        $rootNode->children()
            ->arrayNode('mysql')
                ->children()
                    ->scalarNode('host')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('driver')
                        ->defaultNull()
                        ->end()
                    ->scalarNode('driverClass')
                        ->defaultNull()
                        ->end()
                    ->scalarNode('dbname')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('user')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('password')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('charset')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('mssql')
                ->children()
                    ->scalarNode('host')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('driver')
                        ->defaultNull()
                        ->end()
                    ->scalarNode('driverClass')
                        ->defaultNull()
                        ->end()
                    ->scalarNode('dbname')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('user')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->scalarNode('password')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('mapping')
              ->prototype('array')
                ->children()
                  ->scalarNode('source')
                    ->end()
                  ->scalarNode('destination')->end()
                    ->end();

        return $treeBuilder;
    }
}


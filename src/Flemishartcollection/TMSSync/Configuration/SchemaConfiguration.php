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
 * Database schema configuration class for Symfony\Config.
 *
 * Contains the validation rules for app/config/schema.yml.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class SchemaConfiguration implements ConfigurationInterface {

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('schema');
        $rootNode->children()
            ->arrayNode('tables')
                ->prototype('array')
                    ->children()
                        ->scalarNode('primaryKey')->end()
                        ->arrayNode('columns')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('type')->end()
                                    ->arrayNode('attributes')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('notnull')
                                                    ->defaultTrue()
                                                    ->end()
                                                ->scalarNode('autoincrement')
                                                    ->defaultFalse()
                                                    ->end()
                                                ->scalarNode('unsigned')
                                                    ->defaultNull()
                                                    ->end()
                                                ->scalarNode('length')
                                                    ->defaultNull()
                                                    ->end()
                                            ->end()
                                    ->end()
                                ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}

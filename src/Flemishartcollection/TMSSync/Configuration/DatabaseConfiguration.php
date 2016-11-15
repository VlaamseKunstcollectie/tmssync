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

class DatabaseConfiguration implements ConfigurationInterface {
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
                        ->isRequired()
                        ->cannotBeEmpty()
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
                        ->end();

        return $treeBuilder;
    }
}


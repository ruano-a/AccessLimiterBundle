<?php


namespace ruano_a\AccessLimiterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('access_limiter');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('passwords')
                    ->scalarPrototype()->isRequired()->end()
                ->end() // passwords
                ->booleanNode('active')
                    ->defaultTrue()
                ->end()
                ->scalarNode('template_path')
                    ->defaultValue('@AccessLimiter/gate.html.twig')
                ->end()
                ->integerNode('listener_priority')
                    ->defaultValue(0)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

<?php

namespace Kitpages\ShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kitpages_shop');

        $this->addGeneralSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Parses the kitpages_cms others sections
     * Example for yaml driver:
     * kitpages_shop:
     *     target_parameter: 'cms_target'
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addGeneralSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('order_display_route_name')->cannotBeEmpty()->isRequired()->end()
                ->booleanNode('is_cart_including_vat')->defaultTrue()->end()
                ->scalarNode('from_email')->cannotBeEmpty()->isRequired()->end()
                ->arrayNode('invoice_email_list')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->beforeNormalization()
                        ->ifTrue(function($v){ return !is_array($v); })
                        ->then(function($v){ return array($v); })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()

            ->end();
    }

}

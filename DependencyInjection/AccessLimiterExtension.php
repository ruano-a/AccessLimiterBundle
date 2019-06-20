<?php

namespace ruano_a\AccessLimiterBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class AccessLimiterExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
        	$container,
        	new FileLocator(__DIR__.'/../Resources/config')
    	);
    	$loader->load('services.yaml');
    	$container->setParameter('access_limiter.passwords', $mergedConfig['passwords']);
    	$container->setParameter('access_limiter.active', $mergedConfig['active']);
    	$container->setParameter('access_limiter.template_path', $mergedConfig['template_path']);
    	$container->setParameter('access_limiter.listener_priority', $mergedConfig['listener_priority']);
    }
}
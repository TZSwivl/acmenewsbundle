<?php

namespace Acme\NewsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AcmeNewsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Загружаем сервисы нашего бандла
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        // Читаем и реагируем на раздел конфигурации acme_news
        $config = new Configuration();

        $config = $this->processConfiguration($config, $configs);

        // Заполняем параметры 'acme_news. ...' для использования в service.yml
        $paramsPrefix = $this->getAlias(); // 'acme_news'

        // acme_news.news_per_page....
        foreach ($config['news_per_page'] as $name => $param) {
            $container->setParameter(
                $paramsPrefix . '.news_per_page.' . $name,
                $param
            );
        }

        // acme_news.memcached....
        $memcachedEnabled = $this->isConfigEnabled($container, $config['memcached']);

        $container->setParameter(
            $paramsPrefix . '.memcached.enabled',
            $memcachedEnabled ? true : false
        );
        $container->setParameter(
            $paramsPrefix . '.memcached.service_name',
            $config['memcached']['service_name'] // В любом случае заполняем значением по умолчанию или указанным в конфиге
        );
    }
}
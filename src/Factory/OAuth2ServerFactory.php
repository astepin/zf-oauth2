<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */
namespace ZF\OAuth2\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class OAuth2ServerFactory implements FactoryInterface
{

    /**
     * {@inheritdoc}
     * @return OAuth2ServerInstanceFactory
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $config = isset($config['zf-oauth2']) ? $config['zf-oauth2'] : [];
        return new OAuth2ServerInstanceFactory($config, $container);
    }
}

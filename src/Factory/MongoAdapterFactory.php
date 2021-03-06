<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\OAuth2\Factory;

use Interop\Container\ContainerInterface;
use MongoClient;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF\OAuth2\Adapter\MongoAdapter;
use ZF\OAuth2\Controller\Exception;

/**
 * Class MongoAdapterFactory
 *
 * @package ZF\OAuth2\Factory
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class MongoAdapterFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     * @return MongoAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('Config');
        return new MongoAdapter($this->getMongoDb($container), $this->getOauth2ServerConfig($config));
    }

    /**
     * Get the mongo database
     *
     * @param ContainerInterface $services
     * @return \MongoDB
     */
    protected function getMongoDb($services)
    {
        $config  = $services->get('Config');
        $dbLocatorName = isset($config['zf-oauth2']['mongo']['locator_name'])
            ? $config['zf-oauth2']['mongo']['locator_name']
            : 'MongoDB';

        if ($services->has($dbLocatorName)) {
            return $services->get($dbLocatorName);
        }

        if (!isset($config['zf-oauth2']['mongo']) || empty($config['zf-oauth2']['mongo']['database'])) {
            throw new Exception\RuntimeException(
                'The database configuration [\'zf-oauth2\'][\'mongo\'] for OAuth2 is missing'
            );
        }

        $options = isset($config['zf-oauth2']['mongo']['options']) ? $config['zf-oauth2']['mongo']['options'] : [];
        $options['connect'] = false;
        $server  = isset($config['zf-oauth2']['mongo']['dsn']) ? $config['zf-oauth2']['mongo']['dsn'] : null;
        $mongo   = new MongoClient($server, $options);
        return $mongo->{$config['zf-oauth2']['mongo']['database']};
    }

    /**
     * Retrieve oauth2-server-php configuration
     *
     * @param $config
     * @return array
     */
    protected function getOauth2ServerConfig($config)
    {
        $oauth2ServerConfig = [];
        if (isset($config['zf-oauth2']['storage_settings']) && is_array($config['zf-oauth2']['storage_settings'])) {
            $oauth2ServerConfig = $config['zf-oauth2']['storage_settings'];
        }

        return $oauth2ServerConfig;
    }
}

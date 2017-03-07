<?php

use Doctrine\Common\Cache\FilesystemCache;

class TagManagerTests extends Enlight_Components_Test_Controller_TestCase
{
    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function setUp()
    {
        parent::setUp();

        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('WbmTagManager');
        $this->connection = Shopware()->Container()->get('dbal_connection');
        $this->dispatch('/');
    }

    /**
     * TODO: add tests
     */
}
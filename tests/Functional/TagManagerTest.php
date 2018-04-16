<?php

class TagManagerTests extends Enlight_Components_Test_Controller_TestCase
{
    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var \WbmTagManager\Services\TagManagerVariables
     */
    private $variables;

    public function setUp()
    {
        parent::setUp();

        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('WbmTagManager');
        $this->variables = Shopware()->Container()->get('wbm_tag_manager.variables');
    }

    public function testDataLayerVariables()
    {
        static::saveConfig('wbmTagManagerContainer', 'GTM-XXXXXX');

        $this->dispatch('/');

        $dataLayerVariables = $this->variables->getVariables();

        $this->assertTrue($dataLayerVariables['google_tag_params']['ecomm_pagetype'] === 'home');
    }

    private static function saveConfig($name, $value)
    {
        $formattedValue = sprintf('s:%d:"%s";', strlen($value), $value);
        Shopware()->Db()->query(
            'UPDATE s_core_config_elements SET value = ? WHERE name = ?',
            [$formattedValue, $name]
        );
        Shopware()->Container()->get('cache')->clean();
    }
}

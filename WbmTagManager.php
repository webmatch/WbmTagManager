<?php
/**
 * Tag Manager
 * Copyright (c) Webmatch GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace WbmTagManager;

use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class WbmTagManager
 */
class WbmTagManager extends \Shopware\Components\Plugin
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('wbm_tag_manager.plugin_dir', $this->getPath());

        parent::build($container);
    }

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $sql = file_get_contents($this->getPath() . '/Resources/sql/install.sql');

        $this->container->get('shopware.db')->query($sql);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        $currentVersion = $context->getCurrentVersion();
        $sql = '';

        if (version_compare($currentVersion, '2.0.0', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/install.sql');
        }
        if (version_compare($currentVersion, '2.0.3', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/update.2.0.3.sql');
        }
        if (version_compare($currentVersion, '2.0.4', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/update.2.0.4.sql');
        }
        if (version_compare($currentVersion, '2.1.2', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/update.2.1.2.sql');
        }
        if (version_compare($currentVersion, '2.1.9', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/update.2.1.9.sql');
            $this->container->get('shopware.db')->query($sql);
        }

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
}

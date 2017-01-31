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

namespace WbmTagManager\Subscriber\Backend;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

/**
 * Class TagManagerController
 * @package WbmTagManager\Subscriber\Backend
 */
class TagManagerController implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_WbmTagManager' => 'onWbmTagManagerController'
        ];
    }

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function onWbmTagManagerController()
    {
        $this->container->get('template')->addTemplateDir(
            $this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/views/'
        );

        $this->container->get('snippets')->addConfigDir(
            $this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/snippets/'
        );

        return $this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Controllers/Backend/WbmTagManager.php';
    }
}
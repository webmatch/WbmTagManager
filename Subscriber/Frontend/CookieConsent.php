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

namespace WbmTagManager\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Snippet_Manager;
use Enlight_Controller_ActionEventArgs;
use Shopware\Bundle\CookieBundle\CookieCollection;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;
use Shopware_Components_Config;

class CookieConsent extends ConfigAbstract implements SubscriberInterface
{
    /**
     * @var Enlight_Components_Snippet_Manager
     */
    private $snippets;

    /**
     * @var string
     */
    private $pluginDir;

    public function __construct(Shopware_Components_Config $config, Enlight_Components_Snippet_Manager $snippets, $pluginDir)
    {
        $this->snippets = $snippets;
        $this->pluginDir = $pluginDir;
        parent::__construct($config);

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'CookieCollector_Collect_Cookies' => 'addCookie',
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch'
        ];
    }

    public function onPreDispatch(Enlight_Controller_ActionEventArgs $args)
    {
        $args->getSubject()->View()->addTemplateDir($this->pluginDir . '/Resources/views');
    }

    public function addCookie(): CookieCollection
    {
        $collection = new CookieCollection();

        if (!$this->pluginConfig('wbmTagManagerCookieConsent')) {
            return $collection;
        }

        $pluginNamespace = $this->snippets->getNamespace('frontend/wbm_tag_manager/cookie');

        $collection->add(new CookieStruct(
            'wbm_tag_manager',
            '/^wbm_tag_manager/',
            $pluginNamespace->get('label'),
            CookieGroupStruct::STATISTICS
        ));

        return $collection;
    }
}

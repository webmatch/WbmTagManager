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

/**
 * Class TemplateExtension
 */
class TemplateExtension implements SubscriberInterface
{
    /**
     * @var \Enlight_Template_Manager
     */
    private $template;

    /**
     * @var string
     */
    private $pluginDir;

    /**
     * @param \Enlight_Template_Manager $template
     * @param string                    $pluginDir
     */
    public function __construct(
        \Enlight_Template_Manager $template,
        $pluginDir
    ) {
        $this->template = $template;
        $this->pluginDir = $pluginDir;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
        ];
    }

    public function onPreDispatch()
    {
        $this->template->addTemplateDir(
            $this->pluginDir . '/Resources/views/'
        );
    }
}

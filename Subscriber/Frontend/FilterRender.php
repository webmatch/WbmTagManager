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
use Shopware\Components\DependencyInjection\Container;

/**
 * Class FilterRender
 */
class FilterRender implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * PostDispatch constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Plugins_ViewRenderer_FilterRender' => 'onFilterRender',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @return mixed
     */
    public function onFilterRender(\Enlight_Event_EventArgs $args)
    {
        $source = $args->getReturn();

        if (strpos($source, '<html') === false && !$this->container->get('front')->Request()->isXmlHttpRequest()) {
            return $source;
        }

        $containerId = $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerContainer');
        $prettyPrint = $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerJsonPrettyPrint');

        if (
            $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerActive') &&
            !empty($containerId) &&
            strtolower($this->container->get('front')->Request()->getModuleName()) != 'backend'
        ) {
            if (!$this->container->get('front')->Request()->isXmlHttpRequest() || strpos($source, '<html') !== false) {
                $headTag = file_get_contents($this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/tags/head.html');
                $bodyTag = file_get_contents($this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/tags/body.html');

                $headTag = sprintf($headTag, $containerId);
                $bodyTag = sprintf($bodyTag, $containerId);

                if ($dataLayer = $this->container->get('wbm_tag_manager.variables')->getVariables()) {
                    $headTag = '<script>window.dataLayer = window.dataLayer || [];</script>' .
                        self::prependDataLayer($headTag, $dataLayer, $prettyPrint);
                }

                $source = preg_replace(
                    '/<head>/',
                    '<head>' . $headTag,
                    $source,
                    1
                );

                $source = preg_replace(
                    '/<body([\S\s][^>]*)>/',
                    '<body$1><noscript>' . $bodyTag . '</noscript>',
                    $source,
                    1
                );
            } elseif ($dataLayer = $this->container->get('wbm_tag_manager.variables')->getVariables()) {
                $source = self::prependDataLayer($source, $dataLayer, $prettyPrint);
            }
        }

        return $source;
    }

    /**
     * @param $source
     * @param $dataLayer
     * @param bool $prettyPrint
     *
     * @return string
     */
    public static function prependDataLayer($source, $dataLayer, $prettyPrint = false)
    {
        array_walk_recursive($dataLayer, 'self::castArrayValues');

        return '<script>window.dataLayer.push(' .
            json_encode($dataLayer, ($prettyPrint) ? JSON_PRETTY_PRINT : null) .
            ');</script>' .
            $source;
    }

    /**
     * @param $value
     */
    protected static function castArrayValues(&$value)
    {
        switch (true) {
            case is_array(json_decode($value)):
            case is_int(json_decode($value)):
            case is_float(json_decode($value)):
                $value = json_decode($value);
        }
    }
}

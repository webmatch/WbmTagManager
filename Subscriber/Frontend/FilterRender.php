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
 * @package WbmTagManager\Subscriber\Frontend
 */
class FilterRender implements SubscriberInterface
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
            'Enlight_Plugins_ViewRenderer_FilterRender' => 'onFilterRender'
        ];
    }

    /**
     * PostDispatch constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onFilterRender(\Enlight_Event_EventArgs $args)
    {
        $source = $args->getReturn();

        if(strpos($source, '<html') === false && !$this->container->get('front')->Request()->isXmlHttpRequest()) {
            return $source;
        }

        $containerId = $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerContainer');
        $prettyPrint = $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerJsonPrettyPrint');

        if(
            !empty($containerId) &&
            strtolower($this->container->get('front')->Request()->getModuleName()) != 'backend'
        ) {
            if(!$this->container->get('front')->Request()->isXmlHttpRequest()) {
                $headTag = file_get_contents($this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/tags/head.html');
                $bodyTag = file_get_contents($this->container->getParameter('wbm_tag_manager.plugin_dir') . '/Resources/tags/body.html');

                $headTag = sprintf($headTag, $containerId);
                $bodyTag = sprintf($bodyTag, $containerId);

                $source = str_replace(
                    '<head>',
                    '<head>' . $headTag,
                    $source
                );

                if($dataLayer = $this->container->get('wbm_tag_manager.variables')->getVariables()) {
                    array_walk_recursive($dataLayer, array($this, 'castArrayValues'));

                    $bodyTag = $bodyTag .
                        "<script>dataLayer.push(" .
                        json_encode($dataLayer,($prettyPrint) ? JSON_PRETTY_PRINT : null) .
                        ");</script>";

                    $this->container->get('wbm_tag_manager.variables')->setVariables(null);
                }

                /* split the string contained in $source in three parts:
                 * everything before the <body> tag
                 * the body tag with any attributes in it
                 * everything following the body tag
                 */
                $matches = preg_split('/(<body.*?>)/i', $source, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                if(!empty($matches)) {
                    /* assemble the HTML output back with the iframe code in it */
                    $injectedHTML = $matches[0] . $matches[1] . $bodyTag . $matches[2];

                    return $injectedHTML;
                }
            } else if($dataLayer = $this->container->get('wbm_tag_manager.variables')->getVariables()) {
                array_walk_recursive($dataLayer, array($this, 'castArrayValues'));

                $source = "<script>dataLayer.push(" .
                    json_encode($dataLayer,($prettyPrint) ? JSON_PRETTY_PRINT : null) .
                    ");</script>" .
                    $source;

                $this->container->get('wbm_tag_manager.variables')->setVariables(null);

                return $source;
            }
        }

        return $source;
    }

    /**
     * @param $value
     */
    private function castArrayValues(&$value)
    {
        switch (TRUE) {
            case is_array(json_decode($value)):
            case is_int(json_decode($value)):
            case is_float(json_decode($value)):
                $value = json_decode($value);
        }
    }
}

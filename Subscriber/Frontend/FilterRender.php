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
use WbmTagManager\Services\TagManagerVariables;

/**
 * Class FilterRender
 */
class FilterRender extends ConfigAbstract implements SubscriberInterface
{
    /**
     * @var TagManagerVariables
     */
    private $variables;

    /**
     * @var \Enlight_Controller_Front
     */
    private $front;

    /**
     * @var string
     */
    private $pluginDir;

    /**
     * @param TagManagerVariables $variables
     * @param \Shopware_Components_Config $config
     * @param \Enlight_Controller_Front $front
     * @param string $pluginDir
     */
    public function __construct(
        TagManagerVariables $variables,
        \Shopware_Components_Config $config,
        \Enlight_Controller_Front $front,
        $pluginDir
    )
    {
        $this->variables = $variables;
        $this->front = $front;
        $this->pluginDir = $pluginDir;

        parent::__construct($config);
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
        $request = $this->front->Request();

        if ($this->excludeTraffic()) {
            return $source;
        }

        if (
            (strpos($source, '<html') === false && !$request->isXmlHttpRequest()) ||
            in_array(strtolower($request->getModuleName()), ['widgets', 'backend'])
        ) {
            return $source;
        }

        $containerId = $this->pluginConfig('wbmTagManagerContainer');

        if (!$this->pluginConfig('wbmTagManagerActive') || empty($containerId)) {
            return $source;
        }

        $prettyPrint = $this->pluginConfig('wbmTagManagerJsonPrettyPrint');
        $isAjaxVariant = $request->getControllerName() === 'detail' && $request->get('template') === 'ajax';

        $this->variables->setModule('frontend');

        if ($isAjaxVariant && $this->variables->getVariables()) {
            return $this->includeDataLayerInProductDetail($source, $prettyPrint);
        }

        if (!$request->isXmlHttpRequest() || strpos($source, '<html') !== false) {
            return $this->includeDataLayerInHead($source, $containerId, $prettyPrint);
        }

        if ($this->variables->getVariables()) {
            return $this->variables->prependDataLayer($source, $prettyPrint);
        }

        return $source;
    }

    /**
     * @return bool
     */
    public function excludeTraffic()
    {

        $myIp = $_SERVER['REMOTE_ADDR'];

        $excludeList = $this->pluginConfig('wbmExcludeTraffic');

        $excludeList = explode("\n", $excludeList);

        foreach ($excludeList as $ipMatch) {

            $ipMatch = trim(substr($ipMatch, 0, strpos($ipMatch, "#")));

            preg_match('/' . $ipMatch . '/', $myIp, $matches);

            if ($matches[0]) {
                return true;
            }

        }

        return false;

    }

    /**
     * @param string $source
     * @param string $containerId
     * @param bool $prettyPrint
     *
     * @return string
     */
    public function includeDataLayerInHead(
        $source,
        $containerId,
        $prettyPrint
    )
    {
        $headTag = file_get_contents($this->pluginDir . '/Resources/tags/head.html');
        $bodyTag = file_get_contents($this->pluginDir . '/Resources/tags/body.html');

        $headTag = sprintf($headTag, $containerId);
        $bodyTag = sprintf($bodyTag, $containerId);

        $headTag = $this->wrapHeadTag($headTag);

        if ($this->variables->getVariables()) {
            $headTag = sprintf(
                '%s%s%s%s',
                '<script>',
                'window.dataLayer = window.dataLayer || [];',
                '</script>',
                $this->variables->prependDataLayer($headTag, $prettyPrint)
            );
        }

        $source = $this->injectMarkup($headTag, $source, ['<meta charset="utf-8">', '<head>']);
        $source = $this->injectMarkup($bodyTag, $source, ['</noscript>'], true);

        return $source;
    }

    /**
     * @param string $source
     * @param bool $prettyPrint
     *
     * @return string
     */
    public function includeDataLayerInProductDetail(
        $source,
        $prettyPrint
    )
    {
        $source = $this->injectMarkup(
            $this->variables->prependDataLayer('', $prettyPrint),
            $source,
            ['<div class="product--detail-upper block-group">']
        );

        return $source;
    }

    /**
     * @param string $injection
     * @param string $source
     * @param array $anchors
     * @param bool $before
     *
     * @return string
     */
    private function injectMarkup(
        $injection,
        $source,
        $anchors = [],
        $before = false
    )
    {
        foreach ($anchors as $anchor) {
            $anchorRegex = '/' . str_replace('/', '\/', $anchor) . '/';

            if (preg_match($anchorRegex, $source)) {
                if ($before) {
                    $injection .= $anchor;
                } else {
                    $injection = $anchor . $injection;
                }

                $source = preg_replace(
                    $anchorRegex,
                    $injection,
                    $source,
                    1
                );

                break;
            }
        }

        return $source;
    }

    /**
     * @param string $headTag
     *
     * @return string
     */
    private function wrapHeadTag($headTag)
    {
        $jsBefore = $this->pluginConfig('wbmTagManagerJsBefore');
        $jsAfter = $this->pluginConfig('wbmTagManagerJsAfter');

        if (!empty($jsBefore)) {
            $headTag = sprintf(
                '%s%s%s%s',
                '<script>',
                $jsBefore,
                '</script>',
                $headTag
            );
        }

        if (!empty($jsAfter)) {
            $headTag = sprintf(
                '%s%s%s%s',
                $headTag,
                '<script>',
                $jsAfter,
                '</script>'
            );
        }

        return $headTag;
    }
}

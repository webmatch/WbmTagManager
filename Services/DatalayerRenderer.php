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

namespace WbmTagManager\Services;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use WbmTagManager\Models\Property;

/**
 * Class DatalayerRenderer
 */
class DatalayerRenderer implements DatalayerRendererInterface
{
    /**
     * @var \Enlight_Template_Manager
     */
    private $template;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * TagManagerVariables constructor.
     *
     * @param Container $container
     */
    public function __construct(ModelManager $em, \Enlight_Template_Manager $template)
    {
        $this->template = $template;
        $this->em = $em;
    }

    /**
     * @param $moduleName
     * @param $context
     *
     * @return array
     */
    public function render($moduleName, $context)
    {
        $dataLayerTpl = $this->em->getRepository(Property::class)->getChildrenList(0, $moduleName, true);
        $dataLayer = $this->fillValues($dataLayerTpl, $context);

        array_walk_recursive($dataLayer, 'self::castArrayValues');

        return $dataLayer ?: [];
    }


    /**
     * @param $dataLayerTpl string
     * @param $context array
     *
     * @return array
     */
    private function fillValues($dataLayerTpl, $context)
    {
        $dataLayer = json_encode($dataLayerTpl);

        $search = ['{\/', ',{"endArrayOf":true}'];
        $replace = ['{/', '{/literal}{if !$smarty.foreach.loop.last},{/if}{/foreach}{literal}'];

        $dataLayer = str_replace($search, $replace, $dataLayer);

        while (preg_match('/({"startArrayOf":".*?"},)/i', $dataLayer, $matches)) {
            foreach ($matches as $match) {
                $foreachObj = json_decode(rtrim($match, ','));
                if ($foreachObj->startArrayOf) {
                    $arguments = explode(' as ', $foreachObj->startArrayOf);
                    $dataLayer = str_replace(
                        $match,
                        '{/literal}{foreach from=' . $arguments[0] . ' item=' . ltrim($arguments[1], '$') . ' name=loop}{literal}',
                        $dataLayer
                    );
                }
            }
        }

        $dataLayer = '{literal}' . $dataLayer . '{/literal}';

        $dataLayer = $this->compileString($dataLayer, $context);

        return json_decode($dataLayer, true);
    }

    /**
     * @param $string string
     * @param $context array
     *
     * @return string
     */
    private function compileString($string, $context)
    {
        $view = new \Enlight_View_Default(
            $this->template
        );

        $compiler = new \Shopware_Components_StringCompiler($view->Engine());

        $compiler->setContext($context);

        return $compiler->compileString($string);
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

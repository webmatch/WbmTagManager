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

use Shopware\Components\Model\ModelManager;
use WbmTagManager\Models\Repository;

/**
 * Class TagManagerVariables
 */
class TagManagerVariables implements TagManagerVariablesInterface
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
     * @var array
     */
    private $viewVariables = [];

    /**
     * @var mixed
     */
    private $variables = null;

    /**
     * TagManagerVariables constructor.
     *
     * @param ModelManager              $em
     * @param \Enlight_Template_Manager $template
     */
    public function __construct(
        ModelManager $em,
        \Enlight_Template_Manager $template
    ) {
        $this->template = $template;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getViewVariables()
    {
        return $this->viewVariables;
    }

    /**
     * @param array $viewVariables
     */
    public function setViewVariables($viewVariables)
    {
        $this->viewVariables = $viewVariables;
    }

    /**
     * @return mixed
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param mixed $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param string $module
     */
    public function render($module)
    {
        /** @var Repository $propertyRepo */
        $propertyRepo = $this->em->getRepository('WbmTagManager\Models\Property');
        $dataLayer = $propertyRepo->getChildrenList(0, $module, true);

        if (!empty($dataLayer) && !empty($this->getViewVariables())) {
            $dataLayer = $this->fillValues($dataLayer);

            array_walk_recursive($dataLayer, [$this, 'castArrayValues']);

            $this->setVariables($dataLayer);
        }
    }

    /**
     * @param array $dataLayer
     *
     * @return mixed
     */
    public function fillValues($dataLayer)
    {
        $dataLayer = json_encode($dataLayer);

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

        $dataLayer = $this->compileString($dataLayer);

        return json_decode($dataLayer, true);
    }

    /**
     * @param $source
     * @param bool $prettyPrint
     *
     * @return string
     */
    public function prependDataLayer($source, $prettyPrint = false)
    {
        return '<script>window.dataLayer.push(' .
            json_encode(
                $this->getVariables(),
                ($prettyPrint) ? JSON_PRETTY_PRINT : null
            ) .
            ');</script>' .
            $source;
    }

    /**
     * @param $value
     */
    private function castArrayValues(&$value)
    {
        switch (true) {
            case is_array(json_decode($value)):
            case is_int(json_decode($value)):
            case is_float(json_decode($value)):
                $value = json_decode($value);
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function compileString($string)
    {
        $view = new \Enlight_View_Default(
            $this->template
        );

        $compiler = new \Shopware_Components_StringCompiler($view->Engine());

        $compiler->setContext($this->viewVariables);

        return $compiler->compileString($string);
    }
}

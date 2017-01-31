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
use WbmTagManager\Models\Repository;

/**
 * Class PostDispatch
 * @package WbmTagManager\Subscriber\Frontend
 */
class PostDispatch implements SubscriberInterface
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var mixed
     */
    private $viewVariables;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Widgets' => 'onPostDispatch'
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
     */
    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        $module = strtolower($args->getSubject()->Request()->getModuleName()) .
            '_' . strtolower($args->getSubject()->Request()->getControllerName()) .
            '_' . strtolower($args->getSubject()->Request()->getActionName());

        $search = ['widgets_listing_ajaxlisting'];
        $replace = ['frontend_listing_index'];
        $module = str_replace($search, $replace, $module);

        if($module == 'frontend_checkout_ajaxcart'){
            $module = 'frontend_checkout_' . strtolower($args->getSubject()->Request()->getParam('action'));
        }

        /** @var Repository $propertyRepo */
        $propertyRepo = $this->container->get('models')->getRepository('WbmTagManager\Models\Property');
        $dataLayer = $propertyRepo->getChildrenList(0, $module, true);

        if(!empty($dataLayer)) {
            $this->viewVariables = $args->getSubject()->View()->getAssign();

            $dataLayer = $this->fillValues($dataLayer);

            $this->container->get('wbm_tag_manager.variables')->setVariables($dataLayer);
        }
    }

    /**
     * @param $dataLayer
     * @return mixed
     */
    private function fillValues($dataLayer)
    {
        $dataLayer = json_encode($dataLayer);

        $search = ['{\/', ',{"endArrayOf":true}'];
        $replace = ['{/', '{/literal}{if !$smarty.foreach.loop.last},{/if}{/foreach}{literal}'];

        $dataLayer = str_replace($search, $replace, $dataLayer);

        preg_match('/({"startArrayOf":".*?"},)/i', $dataLayer, $matches);
        foreach($matches as $match){
            $foreachObj = json_decode(rtrim($match, ','));
            if($foreachObj->startArrayOf){
                $arguments = explode(' as ', $foreachObj->startArrayOf);
                $dataLayer = str_replace(
                    $match,
                    '{/literal}{foreach from=' . $arguments[0] . ' item=' . ltrim($arguments[1], '$') . ' name=loop}{literal}',
                    $dataLayer
                );
            }
        }

        $dataLayer = '{literal}' . $dataLayer . '{/literal}';

        $dataLayer = $this->compileString($dataLayer);

        return json_decode($dataLayer);
    }

    /**
     * @param $string
     * @return string
     */
    private function compileString($string)
    {
        $view = new \Enlight_View_Default(
            $this->container->get('Template')
        );
        $view->loadTemplate('frontend/index/index.tpl');

        $compiler = new \Shopware_Components_StringCompiler($view);

        $compiler->setContext($this->viewVariables);

        return $compiler->compileString($string);
    }
}
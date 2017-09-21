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
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();

        $module = $oModule = join('_', [
                strtolower($request->getModuleName()),
                strtolower($request->getControllerName()),
                strtolower($request->getActionName()),
            ]);

        if ($module == 'frontend_checkout_ajaxcart') {
            $module = 'frontend_checkout_' . strtolower($request->getParam('action'));
        }

        $search = [
            'widgets_listing_ajaxlisting',
            'widgets_listing_listingcount',
            'frontend_checkout_ajaxcart',
            'frontend_checkout_ajaxaddarticle',
        ];
        $replace = [
            'frontend_listing_index',
            'frontend_listing_index',
            'frontend_checkout_cart',
            'frontend_checkout_ajaxaddarticlecart',
        ];
        $module = str_replace($search, $replace, $module);

        /** @var Repository $propertyRepo */
        $propertyRepo = $this->container->get('models')->getRepository('WbmTagManager\Models\Property');
        $dataLayer = $propertyRepo->getChildrenList(0, $module, true);

        if (!empty($dataLayer)) {
            $this->viewVariables = $controller->View()->getAssign();

            $dataLayer = $this->fillValues($dataLayer);

            $this->container->get('wbm_tag_manager.variables')->setVariables($dataLayer);
        }

        // Since SW 5.3 the generic listingCountAction is used for paginated listings.
        // Get the response json body, decode it, prepend the dataLayer to the listing key
        // and set json encoded markup as response body.
        if ($oModule == 'widgets_listing_listingcount') {
            /** @var \Enlight_Controller_Response_ResponseHttp $response */
            $response = $controller->Response();
            $data = json_decode($response->getBody(), true);

            if (isset($data['listing'])) {
                if ($dataLayer = $this->container->get('wbm_tag_manager.variables')->getVariables()) {
                    $data['listing'] = FilterRender::prependDataLayer(
                        $data['listing'],
                        $dataLayer,
                        $this->container->get('config')->getByNamespace('WbmTagManager', 'wbmTagManagerJsonPrettyPrint')
                    );

                    $this->container->get('wbm_tag_manager.variables')->setVariables(null);

                    $response->setBody(json_encode($data));
                }
            }
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

        return json_decode($dataLayer, true);
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

        $compiler = new \Shopware_Components_StringCompiler($view->Engine());

        $compiler->setContext($this->viewVariables);

        return $compiler->compileString($string);
    }
}
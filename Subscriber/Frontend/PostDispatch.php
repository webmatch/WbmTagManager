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
 * Class PostDispatch
 */
class PostDispatch implements SubscriberInterface
{
    /**
     * @var TagManagerVariables
     */
    private $variables;

    /**
     * @var \Shopware_Components_Config
     */
    private $config;

    public function __construct(
        TagManagerVariables $variables,
        \Shopware_Components_Config $config
    ) {
        $this->variables = $variables;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Widgets' => 'onPostDispatch',
        ];
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        if (
            !$this->config->getByNamespace('WbmTagManager', 'wbmTagManagerActive') ||
            empty($this->config->getByNamespace('WbmTagManager', 'wbmTagManagerContainer'))
        ) {
            return;
        }

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
            'frontend_checkout_ajax_add_article',
            'frontend_checkout_ajax_delete_article',
        ];
        $replace = [
            'frontend_listing_index',
            'frontend_listing_index',
            'frontend_checkout_cart',
            'frontend_checkout_ajaxaddarticlecart',
            'frontend_checkout_ajaxdeletearticlecart',
        ];
        $module = str_replace($search, $replace, $module);

        $this->variables->setViewVariables($controller->View()->getAssign());
        $this->variables->render($module);

        // Since SW 5.3 the generic listingCountAction is used for paginated listings.
        // Get the response json body, decode it, prepend the dataLayer to the listing key
        // and set json encoded markup as response body.
        if ($oModule == 'widgets_listing_listingcount') {
            /** @var \Enlight_Controller_Response_ResponseHttp $response */
            $response = $controller->Response();
            $data = json_decode($response->getBody(), true);

            if (isset($data['listing'])) {
                if ($dataLayer = $this->variables->getVariables()) {
                    $data['listing'] = FilterRender::prependDataLayer(
                        $data['listing'],
                        $dataLayer,
                        $this->config->getByNamespace('WbmTagManager', 'wbmTagManagerJsonPrettyPrint')
                    );

                    $response->setBody(json_encode($data));
                }
            }
        }
    }
}

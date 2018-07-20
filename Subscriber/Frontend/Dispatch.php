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
 * Class Dispatch
 */
class Dispatch extends ConfigAbstract implements SubscriberInterface
{
    /**
     * @var TagManagerVariables
     */
    private $variables;

    /**
     * @var array
     */
    private $modules;

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @param TagManagerVariables                   $variables
     * @param \Shopware_Components_Config           $config
     * @param array                                 $modules
     * @param \Enlight_Components_Session_Namespace $session
     */
    public function __construct(
        TagManagerVariables $variables,
        \Shopware_Components_Config $config,
        $modules,
        \Enlight_Components_Session_Namespace $session
    ) {
        $this->variables = $variables;
        $this->modules = $modules;
        $this->session = $session;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Widgets' => 'onPostDispatch',
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        ];
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     *
     * @throws \Exception
     */
    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $this->handleDispatch($args);
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     *
     * @throws \Exception
     */
    public function onPreDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $this->getParametersForSession($args->getRequest());
        $this->handleDispatch($args, true);
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     * @param bool                                $isPreDispatch
     *
     * @throws \Exception
     */
    public function handleDispatch(
        \Enlight_Controller_ActionEventArgs $args,
        $isPreDispatch = false
    ) {
        if (
            !$this->pluginConfig('wbmTagManagerActive') ||
            empty($this->pluginConfig('wbmTagManagerContainer'))
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

        $module = $this->rewriteModuleKey(
            $module,
            $request->getParam('action'),
            $request->isDispatched()
        );

        if (!isset($this->modules[$module]) || $this->modules[$module] !== $isPreDispatch) {
            return;
        }

        if (!$this->variables->getVariables()) {
            $this->variables->setViewVariables($controller->View()->getAssign());
            $this->variables->render($module);
        }

        // Since SW 5.3 the generic listingCountAction is used for paginated listings.
        // Get the response json body, decode it, prepend the dataLayer to the listing key
        // and set json encoded markup as response body.
        if ($oModule == 'widgets_listing_listingcount') {
            /** @var \Enlight_Controller_Response_ResponseHttp $response */
            $response = $controller->Response();
            $data = json_decode($response->getBody(), true);

            if (isset($data['listing'])) {
                if ($this->variables->getVariables()) {
                    $data['listing'] = $this->variables->prependDataLayer(
                        $data['listing'],
                        $this->pluginConfig('wbmTagManagerJsonPrettyPrint')
                    );

                    $response->setBody(json_encode($data));
                }
            }
        }
    }

    /**
     * @param string $module
     * @param string $action
     * @param bool   $isDispatched
     *
     * @return string
     */
    private function rewriteModuleKey(
        $module,
        $action,
        $isDispatched
    ) {
        if ($module == 'frontend_checkout_ajaxcart') {
            $module = 'frontend_checkout_' . strtolower($action);
        }

        if ($module == 'frontend_search_defaultsearch' && !$isDispatched) {
            $module = 'frontend_search_index';
        }

        $search = [
            'widgets_listing_ajaxlisting',
            'widgets_listing_listingcount',
            'frontend_checkout_ajaxcart',
            'frontend_checkout_ajax_add_article',
            'frontend_checkout_ajax_delete_article',
            'frontend_checkout_deletearticle',
        ];

        $replace = [
            'frontend_listing_index',
            'frontend_listing_index',
            'frontend_checkout_cart',
            'frontend_checkout_ajaxaddarticlecart',
            'frontend_checkout_ajaxdeletearticlecart',
            'frontend_checkout_ajaxdeletearticlecart',
        ];

        return str_replace($search, $replace, $module);
    }

    /**
     * @param \Enlight_Controller_Request_Request $request
     */
    private function getParametersForSession(\Enlight_Controller_Request_Request $request)
    {
        $parameters = $this->pluginConfig('wbmSessionParameters');

        if (empty($parameters)) {
            return;
        }

        $parameters = explode(',', $parameters);
        $session = $this->session->offsetGet('wbmTagManager') ?: [];

        foreach ($parameters as $parameter) {
            $parameter = trim($parameter);
            $value = $request->getParam($parameter);

            if (empty($value)) {
                continue;
            }

            $session[$parameter] = $value;
        }

        $this->session->offsetSet('wbmTagManager', $session);
    }
}

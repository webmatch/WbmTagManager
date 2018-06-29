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
use WbmTagManager\Models\Module;

/**
 * Class Shopware_Controllers_Backend_WbmTagManagerModules
 */
class Shopware_Controllers_Backend_WbmTagManagerModules extends Shopware_Controllers_Backend_ExtJs
{
    public function indexAction()
    {
        $this->View()->loadTemplate('backend/wbm_tag_manager_modules/app.js');
    }

    public function listAction()
    {
        $qb = $this->container->get('models')->createQueryBuilder();
        $qb->select(
                [
                    'module',
                ]
            )
            ->from(Module::class, 'module');

        $modules = $qb->getQuery()->getArrayResult();

        foreach ($modules as &$module) {
            $module['name'] = $this->container->get('snippets')
                ->getNamespace('backend/plugins/wbm/tagmanager')
                ->get($module['module']);
        }

        $this->View()->assign(
            ['success' => true, 'data' => $modules]
        );
    }

    /**
     * @throws \Exception
     */
    public function saveAction()
    {
        $params = $this->Request()->getPost();
        $id = (int) $this->Request()->get('id');

        $module = $id ?
            $this->container->get('models')->getRepository(Module::class)->find($id) :
            new Module();

        if ($module->getId() && $module->getModule() !== $params['module']) {
            $this->container->get('dbal_connection')->update(
                'wbm_data_layer_properties',
                [
                    'module' => $params['module'],
                ],
                ['module' => $module->getModule()]
            );
        }

        $module->fromArray($params);

        $this->container->get('models')->persist($module);
        $this->container->get('models')->flush();

        $this->container->get('snippets')
            ->getNamespace('backend/plugins/wbm/tagmanager')
            ->set($module->getModule(), $params['name'])
            ->write();

        $this->View()->assign(
            [
                'success' => true,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function deleteAction()
    {
        $id = (int) $this->Request()->get('id');

        /** @var Module $module */
        $module = $this->container->get('models')->getRepository(Module::class)->find($id);

        $this->container->get('dbal_connection')->delete(
            'wbm_data_layer_properties',
            [
                'module' => $module->getModule(),
            ]
        );

        $this->container->get('models')->remove($module);
        $this->container->get('models')->flush();

        $this->View()->assign(
            ['success' => true]
        );
    }
}

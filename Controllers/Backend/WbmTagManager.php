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

use WbmTagManager\Models\Property;

/**
 * Class Shopware_Controllers_Backend_WbmTagManager
 */
class Shopware_Controllers_Backend_WbmTagManager extends Shopware_Controllers_Backend_ExtJs
{
    public function indexAction()
    {
        $this->View()->loadTemplate('backend/wbm_tag_manager/app.js');
    }

    public function listAction()
    {
        $id = (int) $this->Request()->getParam('id', null);
        $module = $this->Request()->getParam('moduleName', null);

        $data = $this->container->get('models')->getRepository('WbmTagManager\Models\Property')->getChildrenList($id, $module);

        $this->View()->assign(
            ['success' => true, 'data' => $data]
        );
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction()
    {
        $params = $this->Request()->getPost();

        $property = new Property();
        $property->fromArray($params);

        $this->container->get('models')->persist($property);
        $this->container->get('models')->flush();

        $this->View()->assign(
            [
                'success' => true,
                'id' => $property->getId(),
            ]
        );
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction()
    {
        $params = $this->Request()->getPost();
        $id = (int) $this->Request()->get('id');

        /** @var Property $property */
        $property = $this->container->get('models')->getRepository('WbmTagManager\Models\Property')->find($id);
        $property->fromArray($params);

        $this->container->get('models')->persist($property);
        $this->container->get('models')->flush();

        $this->View()->assign(
            ['success' => true]
        );
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction()
    {
        $id = (int) $this->Request()->get('id');

        /** @var Property $property */
        $property = $this->container->get('models')->getRepository('WbmTagManager\Models\Property')->find($id);

        $this->container->get('models')->remove($property);
        $this->container->get('models')->flush();

        $this->View()->assign(
            ['success' => true]
        );
    }
}

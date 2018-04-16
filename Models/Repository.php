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

namespace WbmTagManager\Models;

use Shopware\Components\Model\ModelRepository;

/**
 * Class Repository
 */
class Repository extends ModelRepository
{
    public function getChildrenList($id = 0, $module = null, $dataLayer = false)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->from('WbmTagManager\Models\Property', 'property', $dataLayer ? 'property.name' : null);

        $qb->where('property.parentId = :parentId')
            ->setParameter(':parentId', $id)
            ->andWhere('property.moduleName = :moduleName')
            ->setParameter(':moduleName', $module);

        if ($dataLayer) {
            $qb->select(
                [
                    'property.name',
                    'property.value',
                    'property.id',
                ]
            );
            $properties = $qb->getQuery()->getArrayResult();

            foreach ($properties as $key => &$property) {
                $subProperties = $this->getChildrenList($property['id'], $module, true);
                $value = $property['value'];
                unset($property['name']);
                unset($property['value']);
                unset($property['id']);

                if (!empty($subProperties)) {
                    if (empty($value)) {
                        $property = $subProperties;
                    } else {
                        $property = [
                            ['startArrayOf' => $value],
                            $subProperties,
                            ['endArrayOf' => true],
                        ];
                    }
                } else {
                    $property = '{/literal}' . $value . '{literal}';
                }
            }
        } else {
            $qb->select(
                [
                    'property',
                ]
            );
            $properties = $qb->getQuery()->getArrayResult();

            foreach ($properties as &$property) {
                $subProperties = $this->getChildrenList($property['id'], $module);
                $property['children'] = $subProperties;
                $property['expanded'] = true;
                if (empty($subProperties)) {
                    $property['leaf'] = true;
                }
            }
        }

        return $properties;
    }
}

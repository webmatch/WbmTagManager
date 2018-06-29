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

use Doctrine\ORM\Mapping\ClassMetadata;
use Shopware\Components\Model\ModelRepository;

/**
 * Class Repository
 */
class Repository extends ModelRepository
{
    /**
     * @param int  $id
     * @param null $module
     * @param bool $dataLayer
     *
     * @return array
     */
    public function getChildrenList($id = 0, $module = null, $dataLayer = false)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->from(Property::class, 'property', $dataLayer ? 'property.name' : null);

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

    /**
     * @return array
     */
    public function dataForExport()
    {
        $data = [];
        $models = [Module::class, Property::class];

        foreach ($models as $model) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select(
                    [
                        'model',
                    ]
                )
                ->from($model, 'model');

            $data[$model] = $qb->getQuery()->getArrayResult();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importData($data = [])
    {
        foreach ($data as $model => $items) {
            if (!in_array($model, [Module::class, Property::class])) {
                return;
            }

            foreach ($items as $item) {
                /** @var Module|Property $entity */
                $existingEntity = $this->getEntityManager()->getRepository($model)->find($item['id']);

                if (!$existingEntity) {
                    $entity = new $model();
                } else {
                    $entity = $existingEntity;
                }

                $entity->fromArray($item);

                $this->getEntityManager()->persist($entity);

                if (!$existingEntity) {
                    $metadata = $this->getEntityManager()->getClassMetadata($model);
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                }

                $this->getEntityManager()->flush();
            }
        }
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getModules()
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $qb->select(
                [
                    'module',
                    'predispatch',
                ]
            )
            ->from('wbm_data_layer_modules');

        try {
            return $qb->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Exception $exception) {
            return [];
        }
    }
}

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

use Doctrine\DBAL\Connection;

class TagManagerSmarty
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $arguments
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getDbSelect($arguments)
    {
        if (
            empty($arguments['select']) ||
            empty($arguments['from'])
        ) {
            return "";
        }

        $qb = $this->connection->createQueryBuilder();

        $qb->select($arguments['select'])
            ->from($arguments['from']);

        if (is_array($arguments['where'])) {
            $i = 0;
            foreach ($arguments['where'] as $column => $value) {
                $qb->andWhere(sprintf('%s :value%s', $column, $i));
                $qb->setParameter(sprintf('value%s', $i), $value);
                $i++;
            }
        }

        if (is_array($arguments['order'])) {
            foreach ($arguments['order'] as $column => $order) {
                $qb->addOrderBy($column, $order);
            }
        }

        try {
            return $qb->execute()->fetch(\PDO::FETCH_COLUMN);
        } catch (\Exception $exception) {
            return "";
        }
    }
}

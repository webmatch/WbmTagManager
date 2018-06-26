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

class TagManagerSmarty implements TagManagerSmartyInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $_cache = [];

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

        $hash = md5($arguments);

        if (isset($this->_cache[$hash])) {
            return $this->_cache[$hash];
        }

        $qb = $this->connection->createQueryBuilder();

        $qb->select($arguments['select'])
            ->from($arguments['from']);

        if (is_array($arguments['where'])) {
            foreach ($arguments['where'] as $column => $value) {
                $qb->andWhere(
                    sprintf(
                        '%s %s',
                        $column,
                        $qb->createNamedParameter($value)
                    )
                );
            }
        }

        if (is_array($arguments['order'])) {
            foreach ($arguments['order'] as $column => $order) {
                $qb->addOrderBy($column, $order);
            }
        }

        try {
            $value = $qb->execute()->fetch(\PDO::FETCH_COLUMN);
            $this->_cache[$hash] = $value;

            return $value;
        } catch (\Exception $exception) {
            return "";
        }
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function toString($value)
    {
        return sprintf('\"%s\"', $value);
    }
}

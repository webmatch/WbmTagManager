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
     * @var \Enlight_Controller_Front
     */
    private $front;

    /**
     * @var array
     */
    private $_cache = [];

    /**
     * @param Connection                $connection
     * @param \Enlight_Controller_Front $front
     */
    public function __construct(
        Connection $connection,
        \Enlight_Controller_Front $front
    ) {
        $this->connection = $connection;
        $this->front = $front;
    }

    /**
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getDbSelect($arguments)
    {
        if (
            empty($arguments['select']) ||
            empty($arguments['from'])
        ) {
            return '';
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
            return '';
        }
    }

    /**
     * @param $arguments
     *
     * @return string
     */
    public function requestGet($arguments)
    {
        if (
        empty($arguments['param'])
        ) {
            return '';
        }

        return $this->front->Request()->get($arguments['param']);
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

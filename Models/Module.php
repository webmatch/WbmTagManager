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

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="wbm_data_layer_modules")
 */
class Module extends ModelEntity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="module", type="string", length=255, nullable=false)
     */
    private $module;

    /**
     * @var string
     * @ORM\Column(name="variables", type="string", nullable=true)
     */
    private $variables;

    /**
     * @var bool
     * @ORM\Column(name="predispatch", type="boolean", nullable=false)
     */
    private $predispatch = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return bool
     */
    public function isPredispatch()
    {
        return $this->predispatch;
    }

    /**
     * @param bool $predispatch
     */
    public function setPredispatch($predispatch)
    {
        $this->predispatch = $predispatch;
    }
}

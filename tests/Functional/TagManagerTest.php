<?php

class TagManagerTests extends Enlight_Components_Test_Controller_TestCase
{
    public function testDataLayerVariables()
    {
        $this->dispatch('/');

        $this->assertTrue(strpos($this->Response()->getBody(), 'ecomm_pagetype') !== false);
    }
}

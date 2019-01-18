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

use Shopware\Components\Model\ModelRepository;

/**
 * Class TagManagerVariables
 */
class TagManagerVariables implements TagManagerVariablesInterface
{
    /**
     * @var \WbmTagManager\Models\Repository
     */
    private $propertyRepository;

    /**
     * @var \Enlight_Template_Manager
     */
    private $template;

    /**
     * @var TagManagerSmartyInterface
     */
    private $smartyPlugins;

    /**
     * @var string
     */
    private $module = 'frontend';

    /**
     * @var array
     */
    private $viewVariables = [];

    /**
     * @var mixed
     */
    private $variables = null;

    /**
     * TagManagerVariables constructor.
     *
     * @param ModelRepository           $propertyRepository
     * @param \Enlight_Template_Manager $template
     * @param TagManagerSmartyInterface $smartyPlugins
     */
    public function __construct(
        ModelRepository $propertyRepository,
        \Enlight_Template_Manager $template,
        TagManagerSmartyInterface $smartyPlugins
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->template = $template;
        $this->smartyPlugins = $smartyPlugins;
    }

    /**
     * @return array
     */
    public function getViewVariables()
    {
        return $this->viewVariables[$this->module];
    }

    /**
     * @param array $viewVariables
     */
    public function setViewVariables($viewVariables)
    {
        $this->viewVariables[$this->module] = $viewVariables;
    }

    /**
     * @return mixed
     */
    public function getVariables()
    {
        if (!is_array($this->variables)) {
            return null;
        }

        return @$this->variables[$this->module];
    }

    /**
     * @param mixed $variables
     */
    public function setVariables($variables)
    {
        if (!is_array($this->variables)) {
            $this->variables = [];
        }

        $this->variables[$this->module] = $variables;
    }

    /**
     * @param string $module
     *
     * @throws \Exception
     */
    public function render($module)
    {
        $dataLayer = $this->propertyRepository->getChildrenList(0, $module, true);

        if (!empty($dataLayer) && is_array($this->getViewVariables())) {
            $dataLayer = $this->fillValues($dataLayer);

            array_walk_recursive($dataLayer, [$this, 'castArrayValues']);

            $this->setVariables($dataLayer);
        }
    }

    /**
     * @param array $dataLayer
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function fillValues($dataLayer)
    {
        $dataLayer = json_encode($dataLayer);

        $search = ['{\/', ',{"endArrayOf":true}'];
        $replace = ['{/', '{/literal}{if !$smarty.foreach.loop.last},{/if}{/foreach}{literal}'];

        $dataLayer = str_replace($search, $replace, $dataLayer);

        while (preg_match('/({"startArrayOf":".*?"},)/i', $dataLayer, $matches)) {
            foreach ($matches as $match) {
                $foreachObj = json_decode(rtrim($match, ','));
                if ($foreachObj->startArrayOf) {
                    $arguments = explode(' as ', $foreachObj->startArrayOf);
                    $dataLayer = str_replace(
                        $match,
                        '{/literal}{foreach from=' . $arguments[0] . ' item=' . ltrim($arguments[1], '$') . ' name=loop}{literal}',
                        $dataLayer
                    );
                }
            }
        }

        $dataLayer = '{literal}' . $dataLayer . '{/literal}';

        $dataLayer = $this->compileString($dataLayer);

        return json_decode($dataLayer, true);
    }

    /**
     * @param $source
     * @param bool $prettyPrint
     *
     * @return string
     */
    public function prependDataLayer($source, $prettyPrint = false)
    {
        return sprintf(
            '%s%s%s%s',
            '<script>',
            sprintf(
                'window.dataLayer.push(%s);',
                json_encode(
                    $this->getVariables(),
                    ($prettyPrint) ? JSON_PRETTY_PRINT : null
                )
            ),
            '</script>',
            $source
        );
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @param $value
     */
    private function castArrayValues(&$value)
    {
        if (preg_match('/^\"(.*)\"$/', $value)) {
            $value = json_decode($value);

            return;
        }

        switch (true) {
            case is_array(json_decode($value)):
            case is_int(json_decode($value)):
            case is_float(json_decode($value)):
            case is_bool(json_decode($value)):
                $value = json_decode($value);
        }
    }

    /**
     * @param string $string
     *
     * @throws \Exception
     *
     * @return string $string
     */
    private function compileString($string)
    {
        $view = new \Enlight_View_Default(
            $this->template
        );

        $this->registerSmartyPlugins($view->Engine());

        $compiler = new \Shopware_Components_StringCompiler($view->Engine());

        $compiler->setContext($this->getViewVariables());

        $string = $this->stripSlashesFromJsonEncodedSmartySyntax($string, $compiler);

        try {
            return $compiler->compileString($string);
        } catch (\Exception $exception) {
            return json_encode([
               'error' => sprintf('Error while compiling the dataLayer: %s', $exception->getMessage()),
            ]);
        }
    }

    /**
     * @param \Enlight_Template_Manager $view
     *
     * @throws \SmartyException
     */
    private function registerSmartyPlugins($view)
    {
        $plugins = $view->smarty->registered_plugins;

        if (!isset($plugins['function']['dbquery'])) {
            $view->registerPlugin(
                \Smarty::PLUGIN_FUNCTION,
                'dbquery',
                [$this->smartyPlugins, 'getDbSelect']
            );
        }

        if (!isset($plugins['function']['request_get'])) {
            $view->registerPlugin(
                \Smarty::PLUGIN_FUNCTION,
                'request_get',
                [$this->smartyPlugins, 'requestGet']
            );
        }

        if (!isset($plugins['modifier']['to_string'])) {
            $view->registerPlugin(
                \Smarty::PLUGIN_MODIFIER,
                'to_string',
                [$this->smartyPlugins, 'toString']
            );
        }
    }

    /**
     * @param string                              $string
     * @param \Shopware_Components_StringCompiler $compiler
     *
     * @return mixed
     */
    private function stripSlashesFromJsonEncodedSmartySyntax(
        $string,
        \Shopware_Components_StringCompiler $compiler
    ) {
        try {
            $lexer = new \Smarty_Internal_Templatelexer($string, $compiler->getView());
            $codeSnippets = [];
            $i = 0;
            $recording = false;

            while ($lexer->yylex()) {
                // check for key of 'RDEL'
                if ($lexer->token === 17 && $recording) {
                    $recording = false;
                    $i++;
                }
                if ($recording) {
                    $codeSnippets[$i] .= $lexer->value;
                }
                // check for key of 'LDEL', 'LDELIF', 'LDELFOR', 'LDELFOREACH'
                if (in_array($lexer->token, [16, 22, 24, 28]) && !$recording) {
                    $recording = true;
                }
            }

            foreach ($codeSnippets as $codeSnippet) {
                $string = str_replace($codeSnippet, stripslashes($codeSnippet), $string);
            }

            return $string;
        } catch (\Exception $exception) {
            return $string;
        }
    }
}

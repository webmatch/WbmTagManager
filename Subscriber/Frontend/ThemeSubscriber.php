<?php
namespace WbmTagManager\Subscriber\Frontend;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;

class ThemeSubscriber implements SubscriberInterface
{
    /** @var string */
    private $pluginDir;

    public function __construct(string $pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles',
        ];
    }

    public function addJsFiles(): ArrayCollection
    {
        $jsFiles = [];
        $jsDir = $this->pluginDir . '/Resources/views/frontend/_public/src/js/';
        $jsFiles[] = $jsDir . 'jquery.product-click-tracking.js';

        return new ArrayCollection($jsFiles);
    }
}
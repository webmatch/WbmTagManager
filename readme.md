WbmTagManager - GTM integration and dataLayer configuration
=====
**This plugin is currently in beta phase and not yet fully tested**

This plugin integrates a new module within the [Shopware](https://www.shopware.de) backend that allows for the integration 
of the Google Tag Manager Snippet and the configuration of the dataLayer content.

The plugin offers the following features:

* Prepend the GTM Snippet to the head Tag of every page
* Define the content of the dataLayer for any combination of Module/Controller/Action
* All variables passed to the view are available as values for the dataLayer properties
* Use smarty syntax to insert variables and alter values

Requirements
-----
* Shopware >= 5.2.0

Installation
====
Clone this repository into a folder **WbmTagManager** within the **custom/plugins** directory of the Shopware installation.

* Install the plugin through the Plugin-Manager within the Shopware backend. 
* Enter the container ID of the desired GTM Container (found in the GTM backend)
* Activate the plugin and when prompted allow for the clearing of the listed caches.
* Reload the backend to complete the installation.

Usage
=====
The module will be accessible in backend through a new menu point under the Settings menu tab.

The plugin comes pre-configured for the default Enhanced Ecommerce Tracking of Google Analytics.

Use syntax like "$sArticles as $sArticle" as value for properties that are supposed to contain iterative elements.

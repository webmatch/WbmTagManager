WbmTagManager - GTM integration and dataLayer configuration
=====
**This plugin is currently in beta phase and not yet fully tested**

This plugin integrates a new module within the [Shopware](https://www.shopware.de) backend that allows for the integration 
of the Google Tag Manager Snippet and the configuration of the dataLayer content.

![WbmQueryManager](https://www.webmatch.de/wp-content/uploads/2017/02/tag_manager_screen.png)

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

## Install with composer
* Change to your root Installation of shopware
* Run command `composer require webmatch/wbm-tag-manager` and install and activate plugin with Plugin Manager 

Usage
=====
The module will be accessible in backend through a new menu point under the Settings menu tab.

The plugin comes pre-configured for:
* Enhanced Ecommerce Tracking of Google Analytics
* Dynamic Remarketing of Google AdWords

Use syntax like "$sArticles as $sArticle" as value for properties that are supposed to contain iterative elements.

Adding modules
=====
There is currently no UI for adding modules/pages for the dataLayer configuration.
It's scheduled for an upcoming release.

You can however add your own modules by inserting into the corresponding table directly.

A query to do so would look like this:

``` INSERT INTO `wbm_data_layer_modules` ( `module`, `variables`) VALUES ('frontend_forms_index', NULL); ```

Afterwards clear the cache and reload the backend. Open the Tag Manager Backend App and a new tab
will appear without a label. The label can be updated by editing the text snippet within namespace
*/backend/plugins/wbm/tagmanager/* named *frontend_forms_index*

Following this example you could configure a dataLayer that is exclusive to all pages dispatched by
Action *index* of Frontend Controller *forms*

Note: The column *variables* is currently unused and, at some point in the future,
is supposed to contain a preset of available view variables for the corresponding module.
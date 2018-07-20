WbmTagManager - GTM integration and dataLayer configuration
=====
[![Scrutinizer](https://scrutinizer-ci.com/g/webmatch/WbmTagManager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webmatch/WbmTagManager/?branch=master)
[![Travis CI](https://travis-ci.org/webmatch/WbmTagManager.svg?branch=master)](https://travis-ci.org/webmatch/WbmTagManager)

This plugin integrates a new module within the [Shopware](https://www.shopware.de) backend that allows for the integration 
of the Google Tag Manager Snippet and the configuration of the dataLayer content.

![WbmTagManager](https://www.webmatch.de/wp-content/uploads/2017/02/tag_manager_screen.png)

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
Modules are essentially configurations of a dataLayer for a specific view, meaning sites of more or less the same type.

Since version 3.0.0 there's a new UI for adding new modules, as well as updating and deleting existing modules.

When adding new modules you're prompted to enter a name (what the new tab will read) and a key for the module.

The key must contain the values for module, controller and action, connected by underscores. 
See the [Shopware documentation](https://developers.shopware.com/developers-guide/controller/#controller-and-urls) 
for a more detailed explanation. The key must also be all lowercase.

After adding a module you will find a new tab in the main window, where you can declare the properties for the
dataLayer of that specific view.

Additional Smarty functions
=====

{dbquery}
-----

Since version 3.0.0 a new Smarty function `{dbquery}` is available for the compiling of dataLayers. The function allows you to fetch a single value
from any database table. You can pass one or multiple criterias to be used in `WHERE` and `ORDER BY` statements.

The following example will work in the dataLayer of the "Add to basket" module and will fetch the price of the newly added product.

```
{dbquery select='price' from='s_order_basket' where=['ordernumber =' => $smarty.request.sAdd, 'sessionID =' => $smarty.session.Shopware.sessionId] order=['id' => 'DESC']}
```

Please note the use of single quotes only and the array arguments.

Also be aware that this feature is reliant on proper syntax, meaning you have to pass an operator with the column name and the 
columns as well as the values have to exist. Syntax errors will be caught and will result in an empty string to be returned instead.

{request_get}
-----

Request parameters won't be available utilizing `$smarty.request` if they have been set during dispatch or through sluggified URLs (e.g. `/detail/index/sArticle/123`).
By using `request_get` and the `param` argument you can get any parameter available at the time of the dataLayer compiling.

```
{request_get param='sDelete'}
```

{*|to_string}
-----

The modifier `to_string` will force casting of a value to string. The default behaviour is to try and determine the type of
a value by json decoding. If a numeric value should still be recognized as a string, use the modifier after the variable or value.
If you're chaining multiple modifiers, make sure to always use `to_string` in last place.

```
{123|to_string}
=>
"123"
```

Tracking URL Parameters
=====

To track URL parameters (e.g. for campaigns) throughout the user journey, you can set up parameters that
will be temporary saved in the session for use in the dataLayer configuration. 
Within the plugin configuration you can define a comma separated list of GET parameters (e.g. `utm_campaign`, `gclid`, etc).
Those parameters can then be used in the dataLayer configuration by reading the session:
`{$smarty.session.Shopware.wbmTagManager.nameOfTheParameter}`

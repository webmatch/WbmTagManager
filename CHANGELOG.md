## [3.3.0]
### New Feature

- Add support for shopware cookie consent manager (https://docs.shopware.com/en/shopware-5-en/settings/privacy)
- Raise min. compatibility to Shopware `5.6.3`, since Cookie Consent feature is only available from `5.6.3`

### Changes

- Fix some minor issues regarding CI and unit tests

## [3.2.3]
- Cast boolean property values properly
- type hinted interfaces for service decoration

## [3.2.2]
- Filter smarty syntax before compiling and strip json induced escapes
- Added multi-line code editor for property values in backend app

## [3.2.1]
- Use PostDispatchSecure events for controller forwards

## [3.2.0]
- Added dataLayer support for widget views through custom modules

## [3.1.0]
### New Feature

- New plugin configuration setting for comma separated list of GET parameters that will be saved in the session temporary.
  The parameters can then be used in the dataLayer configuration by reading the session:
  `{$smarty.session.Shopware.wbmTagManager.nameOfTheParameter}`

## [3.0.2]

- Minor changes to the backend interface

## [3.0.1]

- Fixed tracking of first page search results

## [3.0.0]
### New Features

- Extended backend app for adding/editing new/existing modules
- Import/Export from/to JSON functions in backend
- New Smarty functions and modifiers available for compiling dataLayers
  - `{dbquery}` query the database
  - `{*|request_get}` get request parameters
  - `{*|to_string}` force cast to string
- Added config fields for inline JS before/after GTM snippet

### Changes

- Moved GTM snippet after `<meta charset>` element if possible on Google's updated directive
- Optional compiling of dataLayers on `preDispatch` events via module setting
- Added/updated default dataLayers with values utilizing `{dbquery}` to fetch additional data for tracking
  - force cast `id` product numbers as strings
  - `category` on `impressions` when loaded through ajax infinite scrolling
  - `price` on `addToCart`
  - `id`, `price` and `quantity` on `removeFromCart`
- Smarty syntax-errors will be caught and will output within the dataLayer instead
- Refactored methods concerning dataLayer compiling into `wbm_tag_manager.variables` service
- Refactored event subscribers with streamlined injection
- Optionally drop database tables on uninstall

## [2.1.9]

- Values for fields in datalayer may now be longer than 255 characters

## [2.1.8]

- Bugfix for multiple array iterations

## [2.1.7]

- Use existing elements for noscript content to avoid regex mishaps in body

## [2.1.6]

- Fixes issues in combination with use of Shopware Advanced Cart (thanks @akkushopJK)

## [2.1.5]

- Support AddToCart Event for deactivated off-canvas basket

## [2.1.4]

- Option to deactive the integration by subshop

## [2.1.3]

- Fixes a bug with the conexco bootstrap theme's ajax filter and full page ajax requests

## [2.1.2]

- Adjusted AddTo/removeFrom basket events for e-commerce tracking

## [2.1.1]

- Check for set Container ID before dataLayer query

## [2.1.0]

- Refactored for Shopware 5.3

## [2.0.4]

- Fixed dataLayer structure for purchase tracking

## [2.0.3]

- Fixed dataLayer structure for detail product impressions

## [2.0.2]

- dataLayer Initialisierung vor GTM Script in Header

## [2.0.1]

- Use allowed modifier only since 5.2.25 in default config

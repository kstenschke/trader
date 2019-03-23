Trader
======

## What does it do?

This was a playground project to experiment w/ stocks.

For the lack of time i'll probably NOT continue working on this.
Before archiving the project in the hope that someone might find it useful, 
i changed the used API (from the now shut-down Yahoo finance API) to IEX Cloud and added a facade, 
so different APIs should be easily connectable.


## Installation

1. Run: composer install
2. Update database configuration in app/config.json
3. Update your IEX Cloud API token in app/config.json
3. Create database as in: app/deploy/install/trader.sql
4. Run: app/deploy/install/import_symbols.php


### Configuration

Configurations for the application (database credentials, navigation elements, available currencies, etc.) 
and debugging/developer settings are set in: app/config.json  


#### Client-side assets - JavaScript and Cascading Stylesheets
   
app/assets/js/lib - 3rd party JavaScript library sources
app/assets/js/    - Application script sources
app/assets/scss   - CSS sources using SASS (automatically compiled w/ leafo/scssphp)
var/assets        - JS and CSS stylesheets (compiled and) loaded by the application.


#### Rebuild JS and CSS after changing sources

To have JS and/or CSS assets rebuilt: delete the .js and/or .css files in the assets directory.

During bootstrap, the application checks this directory and detects whether any assets
needed by the application need to be built. The assets are than compiled, minified and merged 
from the respective sources in app/assets.


#### Adding new JS assets

Add a new file named w/ the prefix: "Trader.".

The assets building process of the bootstrap will recognize new assets by filename, 
no further configuration required.
See: UiScript::mergeAndMinifyJs() in app/models/UiScript.php 


#### Using client-side arguments (config values, translation labels) in JavaScript

The following variables are replaced in JavaScript:

* ###I::<i18n-message>###   - Replaced by translation of <i18n-message> of active language


## Author and License

Written by Kay Stenschke in 2017. Licensed under the MIT License (MIT)

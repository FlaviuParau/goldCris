Blugento PDF
=============

Blugento PDF overwrites standard PDF layouts for invoices, shipments and creditmemos.

Facts
-----
- version: 1.1.0
- extension key: Blugento_Pdf

Description
-----------
Blugento PDF overwrites standard PDF layouts for invoices, shipments and creditmemos. Anyway, you can still use the standard Magento layout, because the extension is highly configurable.

Requirements
------------
- PHP >= 5.2.0
- Mage_Core
- Mage_Pdf
- Mage_Sales

Compatibility
-------------
- Magento >= 1.6

Installation Instructions
-------------------------
1. Install the extension via Magento Connect with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. Configure the extension under System - Configuration - Sales - PDF Print-outs.

###Recommendation
If you use this extension for an austrian shop or Austrian locale (de_AT), please make sure to install [Hackathon_LocaleFallback](https://github.com/magento-hackathon/Hackathon_LocaleFallback) as well, because we only maintain the strings which differ between German locales, so you need this plugin (or have to copy all the strings over). 

Uninstallation
--------------
1. Remove all extension files from your Magento installation.

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/firegento/firegento-pdf/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests). In order to contribute to the latest code, please checkout the `development` branch after cloning your fork.

Developer
---------
Blugento team and all other [contributors]

Copyright
---------
(c) 2015-2016 Blugento Team

**Blugento Compare** is a Magento extension that allows to enable/disable the Magento default Compare functionality, set the maximum allowed items in compare list and the message displayed when the limit is exceeded.

# Usage
The module is created on Magento Community v1.9.2.1

####You can set this values from an external script using this method
```
Mage::helper('blugento_compare')->setState($state)
```
This method will enable/disable the module and set the maximum number or items allowed in compare list according to the declared '$state' value.

The method will aspect and it work with the following '$state' values:

1. To disable the compare send one of this values:  'OFF', '0' or 'DISABLE'
2. To enable the module and set a maximum allowed items, one this values: 1, 2, 3 or 4

# Instalation exemple with composer

```
#!javascript

{
    "require": {
        "magento-hackathon/magento-composer-installer": "dev-master",
        "blugento/blugento_compare": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/magento-hackathon/magento-composer-installer"
        },
        {
            "type": "vcs",
            "url": "https://bitbucket.org/blugento/compare_products.git"
        }
    ],
    "extra": {
        "magento-root-dir": "./"
    }
}
```

# Feedback
If you find any issues or have any suggestions, please get in touch with us through [our site](http://www.mindmagnetsoftware.com).
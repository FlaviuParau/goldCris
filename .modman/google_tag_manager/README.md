Blugento Google Tag Manager
===========================

Install module via modman. After install clear instance cache. From
admin->system->configuration->Google Tag Manager (Blugento tab)->Enabled/Disable
module. After that insert Account Id from Google Tag Manager.

The module add dataLayer scripts on:
1. catalog_category_view - pushes information to dataLayer about 
current category, products from category, filters and sort options.
2. catalog_product_view - pushes information to dataLayer about
current product
3. observes add_to_cart event and pushes information to dataLayer about
current product
4. observes remove_from_cart event and pushes information to dataLayer
5. checkout_onepage_success - pushes information to dataLayer about
current order and products from order
6. default - if customer is loged in, then pushes information to dataLayer
about current customer and information about customers orders
7. observes product_click event and pushes information to dataLayer
about product
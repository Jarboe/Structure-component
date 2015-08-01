# Structure component for Jarboe

### Prepare
1. Create table for structure model by running ```$ jarboe-structure:create-table structure```
2. Run ```$ php artisan jarboe:component check``` to make sure if all is ok
3. Run ```$ php artisan jarboe:component install``` to install components

### Add links to admin panel menu
config ```jarboe.admin.menu```
```php
<?php
return array(
//...
    'menu' => array(
        //...
        \Jarboe\Component\Structure\Util::getNavigationMenuItem(),
        //...
    ),
//...
);
```
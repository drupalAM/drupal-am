# drupal-am
Armenian Drupal Community portal

http://www.drupal.am

## Contributing

To contribute your changes to the project you shoul create a pull request with description of changes. Please see issues section to see current priority.

## Installation
- Clone the project (dev branch)
- Copy default settings file and make sure it has write permission
```sh
 $ cp web/sites/default/default.settings.php web/sites/default/settings.php
 ```
 - Set **'config_sync_directory'** path in **settings.php** file to use existing configuration
 ```sh
$settings['config_sync_directory'] = '../config/sync';
```
 - Install using  **Use existing configuration**

You will have Drupal 8 site with default content and same configuration.

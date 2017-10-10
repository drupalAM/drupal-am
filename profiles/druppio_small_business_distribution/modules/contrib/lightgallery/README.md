The Light Gallery module integrates the jQuery lightGallery plugin with Drupal. lightGallery
is a customizable, modular, responsive, lightbox gallery plugin for jQuery. This module
integrates with the Views module.

## Installation

### Using Composer

 * Edit your project's `composer.json` file and add to the repositories section:
   ```
   "sachinchoolur/lightgallery": {
       "type": "package",
       "package": {
           "name": "sachinchoolur/lightgallery",
           "type": "drupal-library",
           "version": "1.2.21",
           "dist": {
               "url": "https://github.com/sachinchoolur/lightGallery/archive/1.2.21.zip",
               "type": "zip"
           }
       }
   }
   ```
 * Execute `composer require drupal/lightgallery:~1.0`.

### Manually

 * Download the [lightGallery plugin](http://sachinchoolur.github.io/lightGallery/)
   (version 1.2) and place the resulting directory into the libraries directory. Ensure
   `libraries/lightgallery/dist/js/lightgallery.min.js` exists.
 * Download the Light Gallery module and follow the instruction for
   [installing contributed modules](https://www.drupal.org/docs/8/extending-drupal-8/installing-contributed-modules-find-import-enable-configure-drupal-8).

## Usage

 1. When creating a view, select the *LightGallery* format.
 2. Click on the *Settings* link, under the **Format** section.
 3. Scroll down to the *LightGallery Settings* section.
 4. Fill out the settings and apply them to your display.

## Ideas for contributions

Patches are always welcome. Some particular features that will be implemented in the near
future are:

 * Support more Slider LightGallery.

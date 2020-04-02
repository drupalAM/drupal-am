INTRODUCTION
============
The Context group module adds a new option to context module. With this module
you can create custom groups inside reactions. This lets you group multiple
blocks inside one region.


REQUIREMENTS
============
This module requires the following module:
 * Context (http://drupal.org/project/views)


INSTALLATION
============
 * Install as you would normally install a contributed Drupal module. See:
   https://www.drupal.org/docs/8/extending-drupal-8/installing-contributed-
   modules-find-import-enable-configure-drupal-8
   for further information.


CUSTOMIZATION
=============
Context groups uses theme function to render. If you would like to override the
way context groups are rendered you may use template_preprocess_context_groups()
or override one of the context groups template suggestions.
Available templates are:
 * context-groups.html.twig
 * context-groups--{context_id}.html.twig
 * context-groups--{context_group_name}.html.twig
 * context-groups--{context_id}--{context_group_name}.html.twig

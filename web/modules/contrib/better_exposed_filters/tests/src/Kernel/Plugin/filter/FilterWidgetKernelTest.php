<?php

namespace Drupal\Tests\better_exposed_filters\Kernel\Plugin\filter;

use Drupal\Tests\better_exposed_filters\Kernel\BetterExposedFiltersKernelTestBase;
use Drupal\views\Views;

/**
 * Tests the advanced options of a filter widget.
 *
 * @group better_exposed_filters
 *
 * @see \Drupal\better_exposed_filters\Plugin\better_exposed_filters\filter\FilterWidgetBase
 */
class FilterWidgetKernelTest extends BetterExposedFiltersKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $testViews = ['bef_test'];

  /**
   * Tests sorting filter options alphabetically.
   */
  public function testSortFilterOptions() {
    $view = Views::getView('bef_test');
    $display = &$view->storage->getDisplay('default');

    // Get the exposed form render array.
    $output = $this->getExposedFormRenderArray($view);

    // Assert our "field_bef_integer" filter options are not sorted
    // alphabetically, but by key.
    $sorted_options = $options = $output['field_bef_integer_value']['#options'];
    asort($sorted_options);

    $this->assertNotEqual(array_keys($options), array_keys($sorted_options), '"Field BEF integer" options are not sorted alphabetically.');

    $view->destroy();

    // Enable sort for filter options.
    $this->setBetterExposedOptions($view, [
      'filter' => [
        'field_bef_integer_value' => [
          'plugin_id' => 'default',
          'advanced' => [
            'sort_options' => TRUE,
          ],
        ],
      ],
    ]);

    // Get the exposed form render array.
    $output = $this->getExposedFormRenderArray($view);

    // Assert our "field_bef_integer" filter options are sorted alphabetically.
    $sorted_options = $options = $output['field_bef_integer_value']['#options'];
    asort($sorted_options);

    // Assert our "collapsible" options detail is visible.
    $this->assertEqual(array_keys($options), array_keys($sorted_options), '"Field BEF integer" options are sorted alphabetically.');

    $view->destroy();
  }

  /**
   * Tests moving filter option into collapsible fieldset.
   */
  public function testCollapsibleOption() {
    $view = Views::getView('bef_test');
    $display = &$view->storage->getDisplay('default');

    // Enable collapsible options.
    $this->setBetterExposedOptions($view, [
      'filter' => [
        'field_bef_email_value' => [
          'plugin_id' => 'default',
          'advanced' => [
            'collapsible' => TRUE,
          ],
        ],
      ],
    ]);

    // Render the exposed form.
    $this->renderExposedForm($view);

    // Assert our "collapsible" options detail is visible.
    $actual = $this->xpath("//form//details[@id='edit-field-bef-email-value-collapsible']");
    $this->assertEqual(count($actual), 1, '"Field BEF Email" option is displayed as collapsible fieldset.');

    $view->destroy();
  }

}

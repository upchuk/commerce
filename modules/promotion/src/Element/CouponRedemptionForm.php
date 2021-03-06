<?php

namespace Drupal\commerce_promotion\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

@trigger_error('The ' . __NAMESPACE__ . '\CouponRedemptionForm is deprecated. Instead, use the coupon_redemption inline form. See https://www.drupal.org/node/3015309.', E_USER_DEPRECATED);

/**
 * Provides a form element for redeeming a coupon.
 *
 * @deprecated Use the coupon_redemption inline form instead.
 *
 * Usage example:
 * @code
 * $form['coupon'] = [
 *   '#type' => 'commerce_coupon_redemption_form',
 *   '#title' => t('Coupon code'),
 *   '#order_id' => $order_id,
 * ];
 * @endcode
 * The element takes the coupon list from $order->get('coupons').
 * The order is saved when a coupon is added or removed.
 *
 * @FormElement("commerce_coupon_redemption_form")
 */
class CouponRedemptionForm extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#element_ajax' => [],
      // If NULL, the cardinality is unlimited.
      '#cardinality' => 1,
      '#order_id' => NULL,

      '#title' => t('Coupon code'),
      '#process' => [
        [$class, 'processForm'],
      ],
      '#element_validate' => [
        [$class, 'validateForm'],
      ],
      '#theme_wrappers' => ['container'],
    ];
  }

  /**
   * Processes the coupon redemption form.
   *
   * @param array $element
   *   The form element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the #order_id property is empty.
   *
   * @return array
   *   The processed form element.
   */
  public static function processForm(array $element, FormStateInterface $form_state, array &$complete_form) {
    if (empty($element['#order_id'])) {
      throw new \InvalidArgumentException('The commerce_coupon_redemption_form element requires the #order_id property.');
    }

    /** @var \Drupal\commerce\InlineFormManager $inline_form_manager */
    $inline_form_manager = \Drupal::service('plugin.manager.commerce_inline_form');
    $inline_form = $inline_form_manager->createInstance('coupon_redemption', [
      'order_id' => $element['#order_id'],
      'max_coupons' => $element['#cardinality'],
      'ajax_callbacks' => $element['#element_ajax'],
    ]);

    $element['#inline_form'] = $inline_form;
    $element = $inline_form->buildInlineForm($element, $form_state);

    return $element;
  }

  /**
   * Validates the coupon redemption form.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function validateForm(array &$element, FormStateInterface $form_state) {
    /** @var \Drupal\commerce\Plugin\Commerce\InlineForm\InlineFormInterface $inline_form */
    $inline_form = $element['#inline_form'];
    $inline_form->validateInlineForm($element, $form_state);
  }

}

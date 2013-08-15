<?php

/**
 * @file
 * Contains \Drupal\boolean_formatter\Plugin\field\formatter\BooleanYesNo.
 */

namespace Drupal\boolean_formatter\Plugin\field\formatter;

use Drupal\field\Annotation\FieldFormatter;
use Drupal\Core\Annotation\Translation;
use Drupal\field\Plugin\Type\Formatter\FormatterBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Field\FieldInterface;

/**
 * Plugin implementation of the 'boolean_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "boolean_yes_no",
 *   label = @Translation("Yes/No"),
 *   field_types = {
 *     "list_boolean"
 *   },
 *   settings = {
 *     "format" = "yes-no",
 *     "custom_on" = "",
 *     "custom_off" = "",
 *     "reverse" = FALSE
 *   }
 * )
 */
class BooleanYesNo extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    $field_name = $this->fieldDefinition->getFieldName();

    $form['format'] = array(
      '#type' => 'select',
      '#title' => t('Output format'),
      '#options' => boolean_formatter_display_format_options(),
      '#default_value' => $this->getSetting('format'),
    );
    $form['custom_on'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom output for On'),
      '#default_value' => $this->getSetting('custom_on'),
      '#states' => array(
        'visible' => array(
          'select[name="fields[' . $field_name . '][settings_edit_form][settings][format]"]' => array('value' => 'custom'),
        ),
      ),
    );
    $form['custom_off'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom output for Off'),
      '#default_value' => $this->getSetting('custom_off'),
      '#states' => array(
        'visible' => array(
          'select[name="fields[' . $field_name . '][settings_edit_form][settings][format]"]' => array('value' => 'custom'),
        ),
      ),
    );
    $form['reverse'] = array(
      '#type' => 'checkbox',
      '#title' => t('Reverse'),
      '#description' => t('If checked, true will be displayed as false.'),
      '#default_value' => $this->getSetting('reverse'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $formats = boolean_formatter_display_format_options($this->settings);
    $summary[] = t('Output format: %format', array('%format' => $formats[$this->getSetting('format')]));
    $summary[] = t('Reversed: @reversed', array('@reversed' => boolean_formatter_display_value_with_format($this->getSetting('reverse'), 'yes-no', array('reverse' => FALSE))));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(EntityInterface $entity, $langcode, FieldInterface $items) {
    $elements = array();

    $values = options_allowed_values($this->fieldDefinition, $entity);
    foreach ($items as $delta => $item) {
      $value = !empty($values[1]) ? $item->value == $values[1] : !empty($item->value);
      $text = boolean_formatter_display_value_with_format($value, $this->getSetting('format'), $this->settings);
      $elements[$delta] = array('#markup' => field_filter_xss($text));
    }

    return $elements;
  }
}

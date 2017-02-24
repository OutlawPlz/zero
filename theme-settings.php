<?php

use Drupal\Core\Form\FormStateInterface;
use Drupal\zero\Theme\SubThemeGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Implements hook_form_system_theme_settings_alter()
 *
 * @param array $form
 *   Nested array of form elements that comprise the form.
 * @param FormStateInterface $form_state
 *   A keyed array containing the current state of the form.
 */
function zero_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state) {

  /** @var Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
  $module_handler = \Drupal::service('module_handler');

  $svg_icon = TRUE;

  if (!$module_handler->moduleExists('droppy')) {
    drupal_set_message(t('This theme depends on Droppy module. Please, download and install it.'), 'warning');
  }

  if (!$module_handler->moduleExists('svg_icon')) {
    drupal_set_message(t('This theme depends on SVG Icon module. Please, download and install it.'), 'warning');
    $svg_icon = FALSE;
  }

  $form['subtheme'] = array(
    '#type' => 'details',
    '#title' => t('Sub-theme generator'),
    '#weight' => -1,
    '#open' => TRUE
  );

  $form['subtheme']['generate_subtheme'] = array(
    '#type' => 'checkbox',
    '#title' => t('Generate a sub-theme')
  );

  $form['subtheme']['label'] = array(
    '#type' => 'textfield',
    '#title' => t('Name'),
    '#description' => t('The human-readable name of this theme.'),
    '#maxlength' => DRUPAL_EXTENSION_NAME_MAX_LENGTH,
    '#states' => array(
      'visible' => array(
        ':input[name="generate_subtheme"]' => array('checked' => TRUE)
      ),
      'required' => array(
        ':input[name="generate_subtheme"]' => array('checked' => TRUE)
      )
    )
  );

  $form['subtheme']['id'] = array(
    '#type' => 'machine_name',
    '#description' => t('A unique machine-readable name for this theme.'),
    '#maxlength' => DRUPAL_EXTENSION_NAME_MAX_LENGTH,
    '#machine_name' => array(
      'exists' => '\Drupal\zero\Theme\SubThemeGenerator::themeExists',
      'source' => array('subtheme', 'label')
    ),
    '#required' => FALSE,
    '#states' => array(
      'visible' => array(
        ':input[name="generate_subtheme"]' => array('checked' => TRUE)
      ),
      'required' => array(
        ':input[name="generate_subtheme"]' => array('checked' => TRUE)
      )
    )
  );

  $form['subtheme']['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#description' => t('Your sub-theme description. '),
    '#states' => array(
      'visible' => array(
        ':input[name="generate_subtheme"]' => array('checked' => TRUE)
      )
    )
  );

  $form['fixed_navbar'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fixed navbar'),
    '#description' => t('The navbar is fixed on top of the page.'),
    '#default_value' => theme_get_setting('fixed_navbar'),
    '#weight' => -1
  );

  $form['mobile_toggle'] = array(
    '#type' => 'details',
    '#title' => t('Mobile button'),
    '#weight' => -1,
    '#open' => TRUE
  );

  $form['mobile_toggle']['mobile_toggle_label'] = array(
    '#type' => 'textfield',
    '#title' => t('Button label'),
    '#description' => t('The label used by the toggle menu button on small screen devices.'),
    '#default_value' => theme_get_setting('mobile_toggle_label')
  );

  if ($svg_icon) {

    $entities = Drupal\svg_icon\Entity\SvgIcon::loadMultiple();
    $config_list = array();

    /** @var Drupal\svg_icon\Entity\SvgIconInterface $entity */
    foreach ($entities as $entity) {
      $config_list[$entity->get('id')] = $entity->get('label');
    }

    $form['mobile_toggle']['mobile_toggle_svg'] = array(
      '#type' => 'select',
      '#title' => t('SVG Sprite'),
      '#description' => t('Select the SVG sprite file.'),
      '#options' => $config_list,
      '#empty_value' => '',
      '#default_value' => theme_get_setting('mobile_toggle_svg')
    );

    $form['mobile_toggle']['mobile_toggle_icon_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Icon ID'),
      '#description' => t('The ID of the icon to display.'),
      '#default_value' => theme_get_setting('mobile_toggle_icon_id')
    );

    $form['mobile_toggle']['mobile_toggle_icon_only'] = array(
      '#type' => 'checkbox',
      '#title' => t('Icon only'),
      '#description' => t('Display the icon and hide the label.'),
      '#default_value' => theme_get_setting('mobile_toggle_icon_only'),
    );
  }

  $form['#submit'][] = 'zero_form_submit';
}

/**
 * Custom submit function.
 *
 * @param $form
 *   Nested array of form elements that comprise the form.
 * @param FormStateInterface $form_state
 *   A keyed array containing the current state of the form.
 */
function zero_form_submit(array $form, FormStateInterface $form_state) {

  if (!$form_state->getValue('generate_subtheme')) {
    return;
  }

  $subTheme = array(
    'machine_name' => $form_state->getValue('id'),
    'name' => $form_state->getValue('label'),
    'description' => $form_state->getValue('description')
  );

  $fs = new Filesystem();
  $finder = new Finder();
  /** @var \Drupal\Core\Extension\ThemeHandler $themeHandler */
  $themeHandler = \Drupal::service('theme_handler');
  /** @var Drupal\Core\Extension\Extension $starterkit */
  $starterkit = $themeHandler->getTheme('zero_starterkit');

  $subThemeGenerator = new SubThemeGenerator($fs, $finder);
  $subThemeGenerator->generateSubTheme($starterkit, $subTheme);
}
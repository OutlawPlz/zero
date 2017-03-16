<?php

use Drupal\Core\Form\FormStateInterface;
use Drupal\svg_sprite\Entity\SvgSprite;
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

  $svg_sprite_enabled = FALSE;
  $droppy_enabled = FALSE;

  if ($module_handler->moduleExists('droppy')) {
    $droppy_enabled = TRUE;
  }

  if ($module_handler->moduleExists('svg_sprite')) {
    $svg_sprite_enabled = TRUE;
  }

  $form['#submit'][] = 'zero_form_submit';

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

  $form['navbar'] = array(
    '#type' => 'details',
    '#title' => t('Adaptive navbar'),
    '#weight' => -1,
    '#open' => TRUE
  );

  $form['navbar']['fixed_navbar'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fixed navbar'),
    '#description' => t('The navbar is fixed on top of the page.'),
    '#default_value' => theme_get_setting('fixed_navbar')
  );

  $form['navbar']['enable_adaptive_navbar'] = array(
    '#type' => 'checkbox',
    '#title' => t('Adaptive navbar'),
    '#description' => t('Please, download and enable <a href="https://github.com/OutlawPlz/drupal_droppy" target="_blank">Droppy</a> module to make the navbar adaptive.'),
    '#default_value' => $droppy_enabled ? theme_get_setting('enable_adaptive_navbar') : FALSE,
    '#disabled' => $droppy_enabled ? FALSE : TRUE
  );

  // If Droppy module is disabled, return.
  if (!$droppy_enabled) {
    return;
  }

  $form['navbar']['navbar_button_label'] = array(
    '#type' => 'textfield',
    '#title' => t('Button label'),
    '#description' => t('The label used by the toggle menu button on small screen devices.'),
    '#default_value' => theme_get_setting('navbar_button_label'),
    '#states' => array(
      'disabled' => array(
        ':input[name="enable_adaptive_navbar"]' => array('checked' => FALSE)
      )
    )
  );

  $form['navbar']['navbar_button_svg_sprite'] = array(
    '#type' => 'select',
    '#title' => t('SVG Sprite'),
    '#description' => t('Please, download and enable <a href="https://github.com/OutlawPlz/svg_icon" target="_blank">SVG Icon</a> module to use icons in the nabvar button.'),
    '#options' => SvgSprite::getConfigList(),
    '#empty_value' => '',
    '#default_value' => $svg_sprite_enabled ? theme_get_setting('navbar_button_svg_sprite') : FALSE,
    '#disabled' => $svg_sprite_enabled ? FALSE : TRUE,
    '#states' => array(
      'disabled' => array(
        ':input[name="enable_adaptive_navbar"]' => array('checked' => FALSE)
      )
    )
  );

  // If SVG Icon module is disabled, return.
  if (!$svg_sprite_enabled) {
    return;
  }

  $form['navbar']['navbar_button_icon_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Icon ID'),
    '#description' => t('The ID of the icon to display.'),
    '#default_value' => theme_get_setting('navbar_button_icon_id'),
    '#states' => array(
      'disabled' => array(
        ':input[name="enable_adaptive_navbar"]' => array('checked' => FALSE)
      )
    )
  );

  $form['navbar']['navbar_button_icon_right'] = array(
    '#type' => 'checkbox',
    '#title' => t('Icon right'),
    '#description' => t('Print the icon on the right of the label.'),
    '#default_value' => theme_get_setting('navbar_button_icon_right'),
    '#states' => array(
      'disabled' => array(
        ':input[name="enable_adaptive_navbar"]' => array('checked' => FALSE)
      )
    )
  );

  $form['navbar']['navbar_button_hide_label'] = array(
    '#type' => 'checkbox',
    '#title' => t('Hide label'),
    '#description' => t('Display the icon and hide the label.'),
    '#default_value' => theme_get_setting('navbar_button_hide_label'),
    '#states' => array(
      'disabled' => array(
        ':input[name="enable_adaptive_navbar"]' => array('checked' => FALSE)
      )
    )
  );
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

  /** @var \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler */
  $themeHandler = \Drupal::service('theme_handler');

  try {
    $themeHandler->getTheme('zero_starterkit');
  }
  catch (InvalidArgumentException $exception) {
    \Drupal::service('theme_installer')->install(['zero_starterkit']);
  }
  finally {
    /** @var Drupal\Core\Extension\Extension $starterkit */
    $starterkit = $themeHandler->getTheme('zero_starterkit');
  }

  $subThemeGenerator = new SubThemeGenerator($fs, $finder);
  $subThemeGenerator->generateSubTheme($starterkit, $subTheme);
}
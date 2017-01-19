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
    '#title' => t('Navbar'),
    '#weight' => -1,
    '#open' => TRUE
  );

  $form['navbar']['hide_site_name'] = array(
    '#type' => 'checkbox',
    '#title' => t('Hide site name'),
    '#description' => t('The site name is hidden from visualization.'),
    '#default_value' => theme_get_setting('hide_site_name')
  );

  $form['navbar']['fixed_navbar'] = array(
    '#type' => 'checkbox',
    '#title' => t('Fixed navbar'),
    '#description' => t('The navbar is fixed on top of the page.'),
    '#default_value' => theme_get_setting('fixed_navbar')
  );

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
  $themeHandler = \Drupal::service('theme_handler');

  $subThemeGenerator = new SubThemeGenerator($fs, $finder, $themeHandler);

  $subThemeGenerator->generateSubTheme('zero', $subTheme);
}

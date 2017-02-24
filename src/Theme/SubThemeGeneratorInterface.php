<?php
/**
 * @file
 * Contains \Drupal\zero\Theme\SubThemeGeneratorInterface
 */

namespace Drupal\zero\Theme;

use Drupal\Core\Extension\Extension;

/**
 * Interface SubThemeGeneratorInterface. Provides an interface generating a
 * sub-theme.
 *
 * @package Drupal\zero\Theme
 */
interface SubThemeGeneratorInterface {

  /**
   * Generate a sub-theme.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateSubTheme(Extension $starterkit, array $subTheme);
}
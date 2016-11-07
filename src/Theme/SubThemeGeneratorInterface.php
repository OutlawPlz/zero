<?php
/**
 * @file
 * Contains \Drupal\zero\Theme\SubThemeGeneratorInterface
 */

namespace Drupal\zero\Theme;

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
   * @param string $baseThemeId
   *   The machine-readable name of the base theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateSubTheme($baseThemeId, array $subTheme);
}
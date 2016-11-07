<?php

/**
 * @file
 * Contains \Drupal\zero\Theme\SubThemeGenerator
 */

namespace Drupal\zero\Theme;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ThemeHandler;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SubThemeGenerator. Generate a sub-theme.
 *
 * @package Drupal\zero\Theme
 */
class SubThemeGenerator implements SubThemeGeneratorInterface {

  /**
   * @var Filesystem $fs
   *   Provides basic utility to manipulate the file system.
   */
  protected $fs;

  /**
   * @var Finder $finder
   *   Finder allows to build rules to find files and directories.
   */
  private $finder;

  /**
   * @var ThemeHandler $themeHandler
   *   Manages the list of available themes.
   */
  protected $themeHandler;

  /**
   * SubThemeGenerator constructor.
   *
   * @param Filesystem $fs
   *   Provides basic utility to manipulate the file system.
   * @param Finder $finder
   *   Finder allows to build rules to find files and directories.
   * @param ThemeHandler $themeHandler
   *   Manages the list of available themes.
   */
  public function __construct(Filesystem $fs, Finder $finder, ThemeHandler $themeHandler) {

    $this->fs = $fs;
    $this->themeHandler = $themeHandler;
    $this->finder = $finder;
  }

  /**
   * Generate a sub-theme.
   *
   * @param string $baseThemeId
   *   The machine-readable name of the base theme.
   * @param array $subTheme
   *
   * @throws \InvalidArgumentException
   *   Thrown when the base-theme does not exist.
   */
  public function generateSubTheme($baseThemeId, array $subTheme) {

    /*
     * If $baseThemeId is not a valid theme id, the ThemeHandler will throw an
     * InvalidArgumentException.
     */
    $baseTheme = $this->themeHandler->getTheme($baseThemeId);

    $this->copyStarterkit($baseTheme, $subTheme);
    $this->generateInfoYml($subTheme);
    $this->generateLibrariesYml($subTheme);
    $this->generateConfigYml($subTheme);
  }

  /**
   * Copy starterkit to sub-theme folder.
   *
   * @param \Drupal\Core\Extension\Extension $baseTheme
   *   An object representing the base-theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   *
   * @throws IOException
   *   Thrown when starterkit/ folder doesn't exist.
   */
  public function copyStarterkit(Extension $baseTheme, array $subTheme) {

    $starterkit = $baseTheme->getPath() . '/starterkit';

    if (!$this->fs->exists($starterkit)) {
      throw new IOException('The starterkit/ folder doesn\'t exist');
    }

    $this->fs->mirror($starterkit, 'themes/' . $subTheme['machine_name']);
  }

  /**
   * Takes an array representing a theme.info.yml, and writes the settings to
   * the generated sub-theme.
   *
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateInfoYml(array $subTheme) {

    $starterkitInfoPath = 'themes/' . $subTheme['machine_name'] . '/starterkit.info.yml';
    $subThemeInfoPath = 'themes/' . $subTheme['machine_name'] . '/' . $subTheme['machine_name'] . '.info.yml';

    $subThemeInfo = Yaml::parse(file_get_contents($starterkitInfoPath));

    /*
     * Foreach value in the starterkit.info.yml, replace with the value defined
     * in the $subTheme array. If hidden flag is defined, remove it. If
     * libraries is defined, replace all starterkit occurrence with the
     * sub-theme machine-readable name.
     */
    foreach ($subTheme as $item => $value) {
      if (array_key_exists($item, $subThemeInfo)) {
        $subThemeInfo[$item] = $subTheme[$item];
      }
    }

    if (isset($subThemeInfo['hidden'])) {
      unset($subThemeInfo['hidden']);
    }

    if (isset($subThemeInfo['libraries'])) {
      $subThemeInfo['libraries'] = str_replace(
        'starterkit',
        $subTheme['machine_name'],
        $subThemeInfo['libraries']
      );
    }

    file_put_contents($starterkitInfoPath, Yaml::dump($subThemeInfo));

    $this->fs->rename($starterkitInfoPath, $subThemeInfoPath);
  }

  /**
   * Generate libraries file.
   *
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateLibrariesYml(array $subTheme) {

    $starterkitLibrariesPath = 'themes/' . $subTheme['machine_name'] . '/starterkit.libraries.yml';
    $subThemeLibrariesPath = 'themes/' . $subTheme['machine_name'] . '/' . $subTheme['machine_name'] . '.libraries.yml';

    $this->fs->rename($starterkitLibrariesPath, $subThemeLibrariesPath);
  }

  /**
   * Generate files in the config directory.
   *
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateConfigYml(array $subTheme) {

    $starterkitConfigPath = 'themes/' . $subTheme['machine_name'] . '/config';
    $configYmlFiles = $this->finder->files()->in($starterkitConfigPath);

    /** @var SplFileInfo $configYmlFile */
    foreach ($configYmlFiles as $configYmlFile) {

      $filePathname = $starterkitConfigPath . '/' . $configYmlFile->getRelativePathname();
      $fileContent = str_replace(
        'starterkit',
        $subTheme['machine_name'],
        file_get_contents($filePathname)
      );

      file_put_contents($filePathname, $fileContent);

      $filePathrename = str_replace(
        'starterkit',
        $subTheme['machine_name'],
        $filePathname
      );

      $this->fs->rename($filePathname, $filePathrename);
    }
  }

  /**
   * Takes a theme id and check if the theme exists or not.
   *
   * @param string $themeId
   *   The machine-readable name of the theme.
   *
   * @return bool
   *   True if theme exists, false otherwise.
   */
  public static function themeExists($themeId) {

    $theme_list = \Drupal::service('theme_handler')->rebuildThemeData();
    return array_key_exists($themeId, $theme_list);
  }
}

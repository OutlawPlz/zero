<?php

/**
 * @file
 * Contains \Drupal\zero\Theme\SubThemeGenerator
 */

namespace Drupal\zero\Theme;

use Drupal\Core\Extension\Extension;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate a sub-theme.
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
   * SubThemeGenerator constructor.
   *
   * @param Filesystem $fs
   *   Provides basic utility to manipulate the file system.
   * @param Finder $finder
   *   Finder allows to build rules to find files and directories.
   */
  public function __construct(Filesystem $fs, Finder $finder) {

    $this->fs = $fs;
    $this->finder = $finder;
  }

  /**
   * Generate a sub-theme.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the base-theme does not exist.
   */
  public function generateSubTheme(Extension $starterkit, array $subTheme) {

    $this->copyStarterkit($starterkit, $subTheme);
    $this->generateLibrariesYml($starterkit, $subTheme);
    $this->generateBreakpointsYml($starterkit, $subTheme);
    $this->generateConfigYml($starterkit, $subTheme);
    $this->generatePackageJson($starterkit, $subTheme);
    $this->generateInfoYml($starterkit, $subTheme);
  }

  /**
   * Copy starterkit to sub-theme folder.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   *
   * @throws IOException
   *   Thrown when starterkit/ folder doesn't exist.
   */
  public function copyStarterkit(Extension $starterkit, array $subTheme) {

    $this->fs->mirror($starterkit->getPath(), 'themes/' . $subTheme['machine_name']);
  }

  /**
   * Takes an array representing a theme.info.yml, and writes the settings to
   * the generated sub-theme.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateInfoYml(Extension $starterkit, array $subTheme) {

    $starterkitInfoPath = 'themes/' . $subTheme['machine_name'] . '/' . $starterkit->getFilename();
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
        $starterkit->getName(),
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
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateLibrariesYml(Extension $starterkit, array $subTheme) {

    $starterkitLibrariesPath = 'themes/' . $subTheme['machine_name'] . '/' . $starterkit->getName() . '.libraries.yml';
    $subThemeLibrariesPath = 'themes/' . $subTheme['machine_name'] . '/' . $subTheme['machine_name'] . '.libraries.yml';

    $this->fs->rename($starterkitLibrariesPath, $subThemeLibrariesPath);
  }

  /**
   * Generate files in the config directory.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateConfigYml(Extension $starterkit, array $subTheme) {

    $starterkitConfigPath = 'themes/' . $subTheme['machine_name'] . '/config';
    $configYmlFiles = $this->finder->files()->in($starterkitConfigPath);

    /** @var SplFileInfo $configYmlFile */
    foreach ($configYmlFiles as $configYmlFile) {

      $filePathname = $starterkitConfigPath . '/' . $configYmlFile->getRelativePathname();
      $fileContent = str_replace(
        $starterkit->getName(),
        $subTheme['machine_name'],
        file_get_contents($filePathname)
      );

      file_put_contents($filePathname, $fileContent);

      $filePathrename = str_replace(
        $starterkit->getName(),
        $subTheme['machine_name'],
        $filePathname
      );

      $this->fs->rename($filePathname, $filePathrename);
    }
  }

  /**
   * Generate breakpoints file.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generateBreakpointsYml(Extension $starterkit, array $subTheme) {

    $starterkitBreakpointsPath = 'themes/' . $subTheme['machine_name'] . '/' . $starterkit->getName() . '.breakpoints.yml';
    $subThemeBreakpointsPath = 'themes/' . $subTheme['machine_name'] . '/' . $subTheme['machine_name'] . '.breakpoints.yml';

    $fileContent = str_replace(
      $starterkit->getName(),
      $subTheme['machine_name'],
      file_get_contents($starterkitBreakpointsPath)
    );

    file_put_contents($starterkitBreakpointsPath, $fileContent);

    $this->fs->rename($starterkitBreakpointsPath, $subThemeBreakpointsPath);
  }

  /**
   * Generate package.json file.
   *
   * @param \Drupal\Core\Extension\Extension $starterkit
   *   An object representing the starterkit theme.
   * @param array $subTheme
   *   An array containing sub-theme info.
   */
  public function generatePackageJson(Extension $starterkit, array $subTheme) {

    if (!extension_loaded('json')) {
      return;
    }

    $packageJsonPath = 'themes/' . $subTheme['machine_name'] . '/package.json';
    $packageJson = json_decode(file_get_contents($packageJsonPath), TRUE);

    $packageJson['name'] = $subTheme['machine_name'];

    if (isset($packageJson['description'])) {
      $packageJson['description'] = $subTheme['description'];
    }

    file_put_contents($packageJsonPath, json_encode($packageJson));
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
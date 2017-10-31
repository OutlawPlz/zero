# Changelog

All notable changes to this project will be documented in this file.

## Unreleased

Log of unreleased changes.

### Added

- [#5][f28f5b11] - Added default style for sidebars.

  [f28f5b11]: https://github.com/OutlawPlz/zero/issues/5 "Sidebars default style"

### Fixed

- [#4][44fe4b58] - Fixed regions attributes are not printed.

  [44fe4b58]: https://github.com/OutlawPlz/zero/issues/4 "Regions attributes are not printed"

## v0.1.3

Released on **2017/10/16**.

### Added

- Added CSS for `input[type="email"]`.

### Changed

- Improved `CHANGELOG.md` based on Keepachangelog.com site.
- Generates sub-theme in `themes/custom/` folder instead of `themes/` folder.
- Toolbar height defined in em.

### Fixed

- Fixed `SvgSprite` not found when module is disabled.

## v0.1.2

Released on **2017/03/16**.

### Added

- Added states to theme settings form.

### Changed

- Refactor SVG Icon to SVG Sprite.

### Fixed

- Fixed delete svg sprite when in use by the theme.
- Fixed zero_starterkit not found.

## v0.1.1

*Released on 2017/03/06.*

### Added

- Pager styling.
- Field tags styling.
- Node submitted styling.
- Added `Starterkit.breakpoints.yml` file, `generateBreakpointsYml()`.
- Added `settings.yml` to `zero_starterkit`.
- Added `generatePackageJson()` function.
- Added `state.css`.
- Added `ckeditor.scss` to `layout/` folder.

### Changed

- Change gulpfile.js sass function.
- Improved sub-theme generator.
- Refactor starterkit to zero_starterkit.
- Improved Droppy integration.
- Improved SVG Icon integration.

### Fixed

- Fixed navbar position when there is no toolbar.
- Fixed node preview bar.

## v0.1.0

Released on **2017/01/26**.

### Added

- Added modules check.
- Manage menu toggle button in settings.
- Added `screenshot.png`.

### Changed

- Added type `drupal-theme` to `composer.json` file.

### Fixed

- Fixed input password positioning.  
- Fixed components CSS declaration in `libraries.yml`.
- Fixed overlapping of toolbar and navbar.

### Removed

- Removed `hide_site_name` option and relative template.
- Removed Font Awesome.

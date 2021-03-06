# Email Validator Changelog

All notable changes to this project will be documented in this file.

## 1.2.2 - 2018-11-22

### Fixed
- Updated resource URL for disposable email providers which for some reason was changed by the host
- Fetched domains are now encoded to UTF-8 before being inserted into the database

## 1.2.1 - 2018-09-20

### Fixed
- Disabled 'Format Check' light switch field can no longer be toggled
- Provider records weren't working with prefixed DB tables [#13](https://github.com/lukeyouell/craft-emailvalidator/issues/13)

## 1.2.0 - 2018-08-16

### Added
- [Contact Form](https://github.com/craftcms/contact-form) plugin support

### Changed
- Improved appearance of settings

## 1.1.2 - 2018-08-10

### Fixed
- 'Did You Mean' sometimes returned an empty suggestion (e.g. 'user@')

## 1.1.1 - 2018-08-02

### Fixed
- Migration issue

## 1.1.0 - 2018-08-02

### Changed
- Plugin now includes a list of over 6500 free & disposable email providers that can be regularly updated from the CP
- `EmailProviderService` renamed to `ProviderService`
- `EmailValidatorService` renamed to `ValidationService`

## 1.0.4 - 2018-07-08

### Added
- Error translations
- `beforeValidate` and `afterValidate` events

### Changed
- Removed duplicated code

## 1.0.3 - 2018-07-07

### Fixed
- Typo check wasn't respecting settings value

## 1.0.2 - 2018-07-07

### Fixed
- Version mis-match

## 1.0.1 - 2018-07-07

### Fixed
- Invalid composer json

## 1.0.0 - 2018-07-07

### Added
- Initial release

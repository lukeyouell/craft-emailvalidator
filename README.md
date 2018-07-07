<img src="src/icon.svg" alt="icon" width="100" height="100">

# Email Validator plugin for Craft CMS 3

Email address validation for user registrations, custom forms and more.

## Installation

#### Requirements

This plugin requires Craft CMS 3.0.0, or later.

#### Plugin Store

Log into your control panel and click on 'Plugin Store'. Search for 'Email Validator'.

#### Composer

1. Open your terminal and go to your Craft project:

```bash
cd /path/to/project
```

2. Then tell Composer to load the plugin:

```bash
composer require lukeyouell/craft-emailvalidator
```

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Email Validator.

## Configuration

You can toggle which checks are enforced by updating the plugin settings.

The following options are available:

| Name | Default | Description |
| ---- | ------- | ----------- |
| Typo Check & Suggestions | `enabled` | Provide an alternative email suggestion if a typo is detected in the domain part of the email address. |
| MX Records | `disabled` | Allow domains that aren't configured to receive email. |
| Catch All | `enabled` | Allow domains that are configured to catch all incoming mail traffic. |
| Roles | `enabled` | Allow role-based email addresses. |
| Free Providers | `enabled` | Allow email addresses found to be using a free email provider such as Gmail and Yahoo!. |
| Disposable Providers | `disabled` | Allow email addresses found to be using a disposable email provider. |

## Email Validator Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Luke Youell](https://github.com/lukeyouell)

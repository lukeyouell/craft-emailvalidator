<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\services;

use lukeyouell\emailvalidator\EmailValidator;
use lukeyouell\emailvalidator\events\ValidationEvent;

use Craft;
use craft\base\Component;

class ValidationService extends Component
{
    // Constants
    // =========================================================================

    const EVENT_BEFORE_VALIDATE = 'beforeValidate';

    const EVENT_AFTER_VALIDATE = 'afterValidate';

    // Public Properties
    // =========================================================================

    public $settings;

    public $email;

    public $score = 100;

    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->settings = EmailValidator::$plugin->settings;
    }

    public function validateEmail($email = null)
    {
        $this->email = $email;

        // Trigger beforeValidate event
        $event = new ValidationEvent([
            'email' => $this->email,
        ]);
        $self = new static;
        $self->trigger(self::EVENT_BEFORE_VALIDATE, $event);

        $result = [
            'email'        => $this->email,
            'user'         => $this->getUser(),
            'domain'       => $this->getDomain(),
            'did_you_mean' => $this->didYouMean(),
            'format_valid' => $this->checkFormat(),
            'mx_found'     => $this->checkDns(),
            'catch_all'    => $this->isCatchAll(),
            'role'         => $this->isRole(),
            'free'         => $this->isFree(),
            'disposable'   => $this->isDisposable()
        ];

        // Trigger afterValidate event
        $event = new ValidationEvent([
            'email'      => $this->email,
            'validation' => $result,
        ]);
        $self = new static;
        $self->trigger(self::EVENT_AFTER_VALIDATE, $event);

        return $result;
    }

    public function getValidationErrors($email = null)
    {
        $validation = $this->validateEmail($email);

        $errors = [];

        if (!$validation['format_valid']) {
            $errors[] = Craft::t('email-validator', 'Invalid email format.');
        }

        if (!$this->settings->allowNoMX and !$validation['mx_found']) {
            $errors[] = Craft::t('email-validator', 'MX records do not exist for this domain.');
        }

        if (!$this->settings->allowRoles and $validation['role']) {
            $errors[] = Craft::t('email-validator', 'Role-based email addresses are not allowed.');
        }

        if (!$this->settings->allowFree and $validation['free']) {
            $errors[] = Craft::t('email-validator', 'Email addresses supplied by free providers are not allowed.');
        }

        if (!$this->settings->allowDisposable and $validation['disposable']) {
            $errors[] = Craft::t('email-validator', 'Disposable email addresses are not allowed.');
        }

        // Only supply suggestion if there are other errors
        if ($this->settings->typoCheck and count($errors) > 0 and $validation['did_you_mean']) {
            $errors[] = Craft::t('email-validator', 'Did you mean {suggestion}?', [
                'suggestion' => $validation['did_you_mean']
            ]);
        }

        return $errors;
    }

    // Private Methods
    // =========================================================================

    private function getUser()
    {
        try {
            $exploded = explode('@', $this->email);
            $user = $exploded[0];

            return $user;
        } catch (\Exception $e) {
            Craft::error('[getUser] '.$e->getMessage(), 'email-validator');
            return null;
        }
    }

    private function getDomain()
    {
        try {
            $exploded = explode('@', $this->email);
            $domain = $exploded[1];

            return $domain;
        } catch (\Exception $e) {
            Craft::error('[getDomain] '.$e->getMessage(), 'email-validator');
            return null;
        }
    }

    private function checkFormat()
    {
        try {
            $valid = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? true : false;

            return $valid;
        } catch (\Exception $e) {
            Craft::error('[checkFormat] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function checkDns($record = 'MX')
    {
        try {
            $domain = $this->getDomain();

            return checkdnsrr($domain, $record);
        } catch (\Exception $e) {
            Craft::error('[checkDns] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function isCatchAll()
    {
        try {
            $mxRecords = dns_get_record($this->getDomain(), DNS_MX);

            foreach ($mxRecords as $record) {
              if ($record['host'] === '*') {
                  return true;
              }
            }

            return false;
        } catch (\Exception $e) {
            Craft::error('[isCatchAll] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function isRole()
    {
        $user = $this->getUser();

        $roles = [
            'abuse',
            'admin',
            'all',
            'billing',
            'contact',
            'email',
            'enquiries',
            'everyone',
            'ftp',
            'hello',
            'help',
            'info',
            'jobs',
            'list',
            'mail',
            'marketing',
            'media',
            'news',
            'no-reply',
            'noreply',
            'orders',
            'postmaster',
            'privacy',
            'remove',
            'sales',
            'media',
            'subscribe',
            'support',
            'sysadmin',
            'tech',
            'unsubscribe',
            'webmaster',
            'www',
        ];

        return in_array($user, $roles);
    }

    private function isFree()
    {
        try {
            $domain = $this->getDomain();
            $provider = EmailValidator::getInstance()->providerService->getProvider($domain, 'free');

            if ($provider->id) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Craft::error('[isFree] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function isDisposable()
    {
        try {
            $domain = $this->getDomain();
            $provider = EmailValidator::getInstance()->providerService->getProvider($domain, 'disposable');

            if ($provider->id) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Craft::error('[isDisposable] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function didYouMean()
    {
        try {
          $providers = EmailValidator::getInstance()->providerService->getProvidersByType('free');
          $user = $this->getUser();
          $domain = $this->getDomain();

          $shortest = -1;
          $closest = null;

          foreach ($providers as $provider) {
              $distance = levenshtein($provider->domain, $domain);

              // check for an exact match
              if ($distance == 0) {
                  // exact match found, so don't return a suggestion
                  return null;
              }

              // if distance is shortest found so far
              // don't consider suggestions with distance greater than 3
              if (($distance <= $shortest or $shortest < 0) and $distance <= 3) {
                  $closest = $provider;
                  $shortest = $distance;
              }
          }

          if (($shortest > 0) and ($shortest <= 3)) {
              return $user.'@'.$closest;
          } else {
              return null;
          }
        } catch (\Exception $e) {
            Craft::error('[didYouMean] '.$e->getMessage(), 'email-validator');
            return null;
        }
    }
}

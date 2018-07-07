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

use Craft;
use craft\base\Component;

class EmailValidatorService extends Component
{
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

        return $result;
    }

    public function getValidationErrors($email = null)
    {
        $this->email = $email;

        $errors = [];

        if (!$this->checkFormat()) {
            $errors[] = 'Invalid email format.';
        }

        if (!$this->settings->allowNoMX and !$this->checkDns()) {
            $errors[] = 'MX records do not exist for this domain.';
        }

        if (!$this->settings->allowRoles and $this->isRole()) {
            $errors[] = 'Role-based email addresses are not allowed.';
        }

        if (!$this->settings->allowFree and $this->isFree()) {
            $errors[] = 'Email addresses supplied by free providers are not allowed.';
        }

        if (!$this->settings->allowDisposable and $this->isDisposable()) {
            $errors[] = 'Disposable email addresses are not allowed.';
        }

        // Only supply suggestion if there are other errors
        if (count($errors) > 0 and $this->didYouMean()) {
            $errors[] = 'Did you mean '.$this->didYouMean().'?';
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
            $providers = EmailValidator::getInstance()->emailProviderService->freeProviders();
            $domain = $this->getDomain();

            return in_array($domain, $providers);
        } catch (\Exception $e) {
            Craft::error('[isFree] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function isDisposable()
    {
        try {
            $providers = EmailValidator::getInstance()->emailProviderService->disposableProviders();
            $domain = $this->getDomain();

            return in_array($domain, $providers);
        } catch (\Exception $e) {
            Craft::error('[isDisposable] '.$e->getMessage(), 'email-validator');
            return false;
        }
    }

    private function didYouMean()
    {
        try {
          $providers = EmailValidator::getInstance()->emailProviderService->freeProviders();
          $user = $this->getUser();
          $domain = $this->getDomain();

          $shortest = -1;

          foreach ($providers as $provider) {
              $distance = levenshtein($provider, $domain);

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

          return $user.'@'.$closest;
        } catch (\Exception $e) {
            Craft::error('[didYouMean] '.$e->getMessage(), 'email-validator');
            return null;
        }
    }
}

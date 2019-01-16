<?php

namespace lukeyouell\emailvalidator\helpers;

use lukeyouell\emailvalidator\helpers\DomainHelper;
use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

class EmailHelper
{
    // Public Methods
    // =========================================================================

    public static function isValid($email): bool
    {
        $validator = new EmailValidator();
        $valid = $validator->isValid($email, new RFCValidation());

        return $valid;
    }

    public static function getUser($email): string
    {
        try {
            $exploded = explode('@', $email);

            return $exploded[0];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getDomain($email): string
    {
        try {
            $exploded = explode('@', $email);

            return $exploded[1];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function hasMxRecords($email)
    {
        try {
            $domain = self::getDomain($email);

            return DomainHelper::hasDnsRecords($domain, 'MX');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function isCatchAll($email)
    {
        try {
            $domain = self::getDomain($email);
            $records = DomainHelper::getDnsRecords($domain, DNS_MX);

            foreach ($records as $record) {
              if ($record['host'] === '*') {
                  return true;
              }
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function isRole($email)
    {
        try {
            $user = self::getUser($email);

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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function isFree($email)
    {
        try {
            $domain = self::getDomain($email);
            $exists = ProviderRecord::find()
                          ->where([
                              'type'     => ProviderRecord::TYPE_FREE,
                              'provider' => $domain,
                          ])
                          ->exists();

            return $exists;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function isDisposable($email)
    {
        try {
            $domain = self::getDomain($email);
            $exists = ProviderRecord::find()
                          ->where([
                              'type'     => ProviderRecord::TYPE_DISPOSABLE,
                              'provider' => $domain,
                          ])
                          ->exists();

            return $exists;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getSuggestion($email)
    {
        try {
            $user = self::getUser($email);
            $domain = self::getDomain($email);
            $providers = ProviderRecord::find()
                          ->where([
                              'type'     => ProviderRecord::TYPE_FREE,
                              'provider' => $domain,
                          ])
                          ->all();

            $shortest = -1;
            $closest = null;

            foreach ($providers as $provider) {
                $distance = levenshtein($provider->provider, $domain);

                // Check for an exact match
                if ($distance == 0) {
                    // Exact match found, so don't return a suggestion
                    return null;
                }

                // If distance is shortest found so far
                // Don't consider suggestions with distance greater than 3
                if (($distance <= $shortest or $shortest < 0) and $distance <= 3) {
                    $closest = $provider;
                    $shortest = $distance;
                }
            }

            if (($shortest > 0) and ($shortest <= 3)) {
                return $user.'@'.$closest;
            }
            
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

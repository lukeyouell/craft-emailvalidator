<?php

namespace lukeyouell\emailvalidator\helpers;

class DomainHelper
{
    // Public Methods
    // =========================================================================

    public static function isValidDomain($domain): bool
    {
      try {
          return filter_var($domain, FILTER_VALIDATE_DOMAIN);
      } catch (\Exception $e) {
          throw $e;
      }
    }

    public static function hasDnsRecords($domain, $type)
    {
        try {
            return checkdnsrr($domain, $type);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getDnsRecords($domain, $type)
    {
        try {
            return dns_get_record($domain, $type);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

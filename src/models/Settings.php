<?php

namespace lukeyouell\emailvalidator\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $providerSources = [
        'free' => 'https://gist.githubusercontent.com/tbrianjones/5992856/raw/free_email_provider_domains.txt',
        'disposable' => 'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf',
    ];

    // Public Methods
    // =========================================================================

    public function rules()
    {
        return [
            ['providerSources', 'each', 'rule' => ['string']],
        ];
    }
}

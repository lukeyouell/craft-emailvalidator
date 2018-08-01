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
use lukeyouell\emailvalidator\models\Provider as ProviderModel;

use Craft;
use craft\base\Component;
use craft\db\Query;

use yii\base\Exception;

class ProviderService extends Component
{
    // Public Methods
    // =========================================================================

    public function getProvidersByType($type)
    {
        $rows = $this->_createProviderQuery()
            ->where(['type' => $type])
            ->all();

        $providers = [];

        foreach ($rows as $row) {
            $providers[] = new ProviderModel($row);
        }

        return $providers;
    }

    public function getProviderByDomain($domain)
    {
        $result = $this->_createProviderQuery()
            ->where(['domain' => $domain])
            ->one();

        return new ProviderModel($result);
    }

    public function getProvider($domain, $type)
    {
        $result = $this->_createProviderQuery()
            ->where([
                'domain' => $domain,
                'type'   => $type,
            ])
            ->one();

        return new ProviderModel($result);
    }

    // Private Methods
    // =========================================================================

    private function _createProviderQuery()
    {
        return (new Query())
            ->select([
                'ev_providers.id',
                'ev_providers.type',
                'ev_providers.domain',
            ])
            ->orderBy('dateCreated')
            ->from(['{{%ev_providers}}']);
    }
}

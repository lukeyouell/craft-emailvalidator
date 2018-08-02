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
use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;

use yii\base\Exception;
use yii\base\InvalidConfigException;

class RecordService extends Component
{
    // Public Properties
    // =========================================================================

    public $plugin;

    public $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->plugin = EmailValidator::getInstance();
        $this->settings = $this->plugin->getSettings();

        if (!$this->settings->validate()) {
            throw new InvalidConfigException('Email Validator settings don’t validate.');
        }
    }

    public function updateProviders($type = 'free')
    {
        // Set source
        $source = null;

        if ($type == 'free') {
            $source = 'https://gist.githubusercontent.com/tbrianjones/5992856/raw/free_email_provider_domains.txt';
        } elseif ($type == 'disposable') {
            $source = 'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blacklist.conf';
        }

        if ($source) {
            // Get domains
            $domains = $this->_getDomains($source);

            if ($domains) {
                $count = 0;

                foreach ($domains as $domain) {
                    $provider = EmailValidator::getInstance()->providerService->getProvider($domain, $type);

                    if (!$provider) {
                        $provider = new ProviderModel();
                    }

                    $provider->type   = $type;
                    $provider->domain = $domain;

                    // Save it
                    $save = $this->saveProvider($provider);

                    if ($save) {
                        $count = $count + 1;
                    }
                }

                // Update 'providersLastUpdated' settings value
                $this->providersLastUpdated();

                Craft::info($count.' '.$type.' providers updated.', __METHOD__);
                return true;
            }
        }

        return false;
    }

    public function saveProvider(ProviderModel $model, bool $runValidation = true)
    {
        if ($model->id) {
            $record = ProviderRecord::findOne($model->id);

            if (!$record) {
                throw new Exception(Craft::t('emailvalidator',
                    'No provider exists with the ID "{id}"',
                    ['id' => $model->id]
                ));
            }
        } else {
            $record = new ProviderRecord();
        }

        if ($runValidation && !$model->validate()) {
            Craft::info('Provider not saved due to a validation error.', __METHOD__);
            return false;
        }

        $record->type   = $model->type;
        $record->domain = $model->domain;

        // Save it
        $record->save(false);

        // Now that we have a record id, save it on the model
        $model->id = $record->id;

        return true;
    }

    public function providersLastUpdated()
    {
        $datetime = DateTimeHelper::currentUTCDateTime();
        $now = $datetime->format('Y-m-d H:i:s');

        Craft::$app->plugins->savePluginSettings($this->plugin, [
            'providersLastUpdated' => $now,
        ]);

        return $now;
    }

    // Private Methods
    // =========================================================================

    private function _getDomains($uri) {
        $client = new \GuzzleHttp\Client([
            'timeout'  => 10,
        ]);

        try {
            $res = $client->get($uri);

            if ($res->getStatusCode() == 200) {
                $body = $res->getBody();
                $domains = StringHelper::split($body, PHP_EOL);

                // Validate domains
                foreach ($domains as $key => $val) {
                    // Remove invalid domains
                    if (!filter_var($val, FILTER_VALIDATE_DOMAIN)) {
                        unset($domains[$key]);
                    }
                }

                return $domains;
            } else {
                // Log error
                Craft::error($response->getStatusCode().' - '.$response->getReasonPhrase(), 'email-validator');
                return false;
            }
        } catch (\Exception $e) {
            // Log error
            Craft::error($e->getMessage(), 'email-validator');
            return $e->getMessage();
        }
    }
}

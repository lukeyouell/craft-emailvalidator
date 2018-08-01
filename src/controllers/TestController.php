<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\controllers;

use lukeyouell\emailvalidator\EmailValidator;
use lukeyouell\emailvalidator\models\Provider as ProviderModel;

use Craft;
use craft\helpers\StringHelper;
use craft\web\Controller;

use yii\web\Response;

class TestController extends Controller
{
    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = ['free', 'disposable'];

    // Public Properties
    // =========================================================================

    public $settings;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->settings = EmailValidator::$plugin->getSettings();
        if (!$this->settings->validate()) {
            throw new InvalidConfigException('Email Validator settings don’t validate.');
        }
    }

    public function actionFree()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://gist.githubusercontent.com',
            'timeout'  => 10,
        ]);

        try {
            $res = $client->request('GET', '/tbrianjones/5992856/raw/free_email_provider_domains.txt');

            if ($res->getStatusCode() == 200) {
                $body = $res->getBody();
                $domains = StringHelper::split($body, PHP_EOL);

                $return = '';

                foreach ($domains as $domain) {
                    $provider = EmailValidator::getInstance()->emailProviderService->getProvider($domain, 'free');

                    if (!$provider) {
                        $provider = new ProviderModel();
                    }

                    $provider->type   = 'free';
                    $provider->domain = $domain;

                    // Save it
                    $save = EmailValidator::getInstance()->emailProviderService->saveProvider($provider);

                    if ($save) {
                        $return .= $domain.' saved.<br>--<br>';
                    } else {
                        $return .= $domain.' not saved.<br>--<br>';
                    }
                }

                return $return;
            } else {
                return $res->getStatusCode();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionDisposable()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://raw.githubusercontent.com',
            'timeout'  => 10,
        ]);

        try {
            $res = $client->request('GET', '/martenson/disposable-email-domains/master/disposable_email_blacklist.conf');

            if ($res->getStatusCode() == 200) {
                $body = $res->getBody();
                $domains = StringHelper::split($body, PHP_EOL);

                $return = '';

                foreach ($domains as $domain) {
                    $provider = EmailValidator::getInstance()->emailProviderService->getProvider($domain, 'disposable');

                    if (!$provider) {
                        $provider = new ProviderModel();
                    }

                    $provider->type   = 'disposable';
                    $provider->domain = $domain;

                    // Save it
                    $save = EmailValidator::getInstance()->emailProviderService->saveProvider($provider);

                    if ($save) {
                        $return .= $domain.' saved.<br>--<br>';
                    } else {
                        $return .= $domain.' not saved.<br>--<br>';
                    }
                }

                return $return;
            } else {
                return $res->getStatusCode();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

<?php

namespace lukeyouell\emailvalidator\jobs;

use lukeyouell\emailvalidator\EmailValidator;
use lukeyouell\emailvalidator\db\Table;
use lukeyouell\emailvalidator\helpers\DomainHelper;
use lukeyouell\emailvalidator\models\Provider as ProviderModel;

use Craft;
use craft\helpers\StringHelper;
use craft\queue\BaseJob;

use GuzzleHttp\Exception\RequestException;

class UpdateProviders extends BaseJob
{
    // Public Properties
    // =========================================================================

    public $type;

    // Public Methods
    // =========================================================================

    public function execute($queue)
    {
        $plugin = EmailValidator::getInstance();
        $settings = $plugin->getSettings();

        $source = $settings->providerSources[$this->type];
        $providers = $this->getProviders($source);

        $count = 1;

        if ($providers) {
            $totalProviders = count($providers);

            foreach ($providers as $domain) {
                // Set progress
                $this->setProgress($queue, $count / $totalProviders);
                $count++;

                // Populate model
                $provider = new ProviderModel();
                $provider->type = $this->type;
                $provider->domain = $domain;

                // Upsert provider
                $this->upsert($provider);
            }
        }
    }

    // Protected Methods
    // =========================================================================

    protected function defaultDescription(): string
    {
        return Craft::t('email-validator', 'Updating '.$this->type.' email providers.');
    }

    // Private Methods
    // =========================================================================

    private function getProviders($source)
    {
        $response = $this->request('get', $source);

        $body = (string)$response->getBody()->getContents();
        $providers = StringHelper::split($body, PHP_EOL);

        // Validate domains
        foreach ($providers as $key => $val) {
            if (!DomainHelper::isValidDomain($val)) {
                unset($providers[$key]);
            } else {
                // Convert to utf-8
                $providers[$key] = utf8_encode($val);
            }
        }

        return $providers;
    }

    public function request($method, $uri, $options = [])
    {
        $client = Craft::createGuzzleClient();
        $e = null;

        try {
            $response = $client->request($method, $uri, $options);
        } catch (RequestException $e) {
            throw $e;
        }

        return $response;
    }

    private function upsert($provider)
    {
        Craft::$app->getDb()->createCommand()
            ->upsert(
                Table::PROVIDERS,
                [
                    'type'     => $provider->type,
                    'provider' => $provider->domain,
                ])
            ->execute();
    }
}

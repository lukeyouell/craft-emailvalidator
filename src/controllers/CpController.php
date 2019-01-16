<?php

namespace lukeyouell\emailvalidator\controllers;

use lukeyouell\emailvalidator\EmailValidator;
use lukeyouell\emailvalidator\assetbundles\CpBundle;
use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;

class CpController extends Controller
{
    // Public Properties
    // =========================================================================

    public $variables;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $plugin = EmailValidator::getInstance();
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($plugin->handle));

        $this->variables = [
            'title'     => $plugin->name,
            'settings'  => $plugin->settings,
            'overrides' => array_keys($overrides),
            'crumbs'    => [
                [
                    'label' => Craft::t('app', 'Settings'),
                    'url'   => UrlHelper::cpUrl('settings'),
                ],
                [
                    'label' => $plugin->name,
                    'url'   => UrlHelper::cpUrl('email-validator'),
                ],
            ],
            'navItems'  => [
                'overview'  => ['title' => 'Overview', 'url' => 'email-validator'],
                'rulesets'  => ['title' => 'Rulesets', 'url' => 'email-validator/rulesets'],
                'providers' => ['title' => 'Providers', 'url' => 'email-validator/providers'],
                'logs'      => ['title' => 'Logs', 'url' => 'email-validator/logs'],
            ],
        ];
    }

    public function actionOverview(array $variables = [])
    {
        $variables = array_merge($this->variables, $variables, [
            'title'        => 'Overview',
            'selectedItem' => 'overview',
        ]);

        return $this->renderTemplate('email-validator/_overview/index', $variables);
    }

    public function actionRulesets(array $variables = [])
    {
        $variables = array_merge($this->variables, $variables, [
            'title'        => 'Rulesets',
            'selectedItem' => 'rulesets',
        ]);

        return $this->renderTemplate('email-validator/_rulesets/index', $variables);
    }

    public function actionProviders(array $variables = [])
    {
        $variables = array_merge($this->variables, $variables, [
            'title'        => 'Providers',
            'selectedItem' => 'providers',
            'count'        => [
                'total'       => EmailValidator::$plugin->providers->countProviders(),
                'free'        => EmailValidator::$plugin->providers->countProviders(ProviderRecord::TYPE_FREE),
                'disposable'  => EmailValidator::$plugin->providers->countProviders(ProviderRecord::TYPE_DISPOSABLE),
            ],
            'providers'    => EmailValidator::$plugin->providers->getProviders(),
        ]);

        $this->view->registerAssetBundle(CpBundle::class);

        return $this->renderTemplate('email-validator/_providers/index', $variables);
    }

    public function actionLogs(array $variables = [])
    {
        $variables = array_merge($this->variables, $variables, [
            'title'        => 'Logs',
            'selectedItem' => 'logs',
        ]);

        return $this->renderTemplate('email-validator/_logs/index', $variables);
    }
}

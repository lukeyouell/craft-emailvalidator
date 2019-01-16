<?php

namespace lukeyouell\emailvalidator;

use lukeyouell\emailvalidator\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\web\UrlManager;

use yii\base\Event;

class EmailValidator extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    // Public Properties
    // =========================================================================

    public $schemaVersion = '1.1.0';

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Install our event listeners
        $this->installEventListeners();

        // Install components
        $this->installComponents();

        // Register Twig Extensions
        $this->registerTwigExtensions();

        // Log that the plugin has been loaded
        $this->logPluginLoad();
    }

    // Protected Methods
    // =========================================================================

    protected function installEventListeners()
    {
        // After plugin installation
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/email-validator'))->send();
                }
            }
        );

        // Register CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['email-validator'] = 'email-validator/cp/overview';
                $event->rules['email-validator/rulesets'] = 'email-validator/cp/rulesets';
                $event->rules['email-validator/providers'] = 'email-validator/cp/providers';
                $event->rules['email-validator/logs'] = 'email-validator/cp/logs';
            }
        );
    }

    protected function installComponents()
    {
        $this->setComponents([
            'providers' => \lukeyouell\emailvalidator\services\Providers::class,
        ]);
    }

    protected function registerTwigExtensions()
    {
        Craft::$app->view->registerTwigExtension(new \lukeyouell\emailvalidator\twig\EmailValidatorTwigExtension());
    }

    protected function logPluginLoad()
    {
        Craft::info(Craft::t('email-validator', '{name} plugin loaded', ['name' => $this->name]));
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
       return Craft::$app->view->renderTemplate('email-validator/settings');
    }
}

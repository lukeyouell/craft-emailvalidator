<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator;

use lukeyouell\emailvalidator\twigextensions\EmailValidatorTwigExtension;
use lukeyouell\emailvalidator\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\elements\User;
use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\web\UrlManager;

use yii\base\Event;
use yii\base\ModelEvent;

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

        Craft::$app->view->registerTwigExtension(new EmailValidatorTwigExtension());

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['settings/email-validator/general'] = 'email-validator/settings/general';
                $event->rules['settings/email-validator/providers'] = 'email-validator/settings/providers';
            }
        );

        // Redirect to settings after installation
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/email-validator'))->send();
                }
            }
        );

        // Validate email address on user save
        Event::on(User::class, User::EVENT_BEFORE_VALIDATE, function(ModelEvent $e) {
            $user = $e->sender;
            $errors = $this->validationService->getValidationErrors($user->email);

            foreach ($errors as $error) {
                $user->addError('email', $error);
            }
        });

        // Register components
        $this->setComponents([
            'validationService'   => \lukeyouell\emailvalidator\services\ValidationService::class,
            'providerService'     => \lukeyouell\emailvalidator\services\ProviderService::class,
            'recordService'       => \lukeyouell\emailvalidator\services\RecordService::class,
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
       return Craft::$app->view->renderTemplate('email-validator/settings');
    }
}

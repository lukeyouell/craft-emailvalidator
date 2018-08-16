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

use Craft;
use craft\web\Controller;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class SettingsController extends Controller
{
    // Public Properties
    // =========================================================================

    public $plugin;

    public $settings;

    public $overrides;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->plugin = EmailValidator::$plugin;
        $this->settings = $this->plugin->getSettings();
        $this->overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->plugin->handle));

        if (!$this->settings->validate()) {
            throw new InvalidConfigException('Email Validator settings don’t validate.');
        }
    }

    public function actionGeneral()
    {
        $variables = [
          'plugin'    => $this->plugin,
          'settings'  => $this->settings,
          'overrides' => $this->overrides,
        ];

        return $this->renderTemplate('email-validator/_settings/general/index', $variables);
    }

    public function actionProviders()
    {
      $variables = [
        'plugin'                  => $this->plugin,
        'settings'                => $this->settings,
        'freeProviderCount'       => $this->plugin->providerService->countProvidersByType('free'),
        'disposableProviderCount' => $this->plugin->providerService->countProvidersByType('disposable'),
      ];

      return $this->renderTemplate('email-validator/_settings/providers/index', $variables);
    }
}

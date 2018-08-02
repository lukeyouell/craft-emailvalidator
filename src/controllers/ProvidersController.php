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

use yii\web\Response;

class ProvidersController extends Controller
{
    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = ['ajax'];

    // Public Methods
    // =========================================================================

    public function actionAjax()
    {
        EmailValidator::getInstance()->recordService->updateProviders('free');
        EmailValidator::getInstance()->recordService->updateProviders('disposable');

        return true;
    }

    public function actionDisposable()
    {
        $response = EmailValidator::getInstance()->recordService->updateProviders('disposable');

        if ($response) {
            return 'success!';
        } else {
            return 'failed.';
        }
    }
}

<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\models;

use lukeyouell\emailvalidator\EmailValidator;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $typoCheck = true;

    public $allowNoMX = false;

    public $allowCatchAll = true;

    public $allowRoles = true;

    public $allowFree = true;

    public $allowDisposable = false;

    public $providersLastUpdated = null;

    public $cf_allowNoMX = false;

    public $cf_allowCatchAll = true;

    public $cf_allowRoles = true;

    public $cf_allowFree = true;

    public $cf_allowDisposable = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['typoCheck', 'allowNoMX', 'allowCatchAll', 'allowRoles', 'allowFree', 'allowDisposable'], 'boolean'],
            [['cf_allowNoMX', 'cf_allowCatchAll', 'cf_allowRoles', 'cf_allowFree', 'cf_allowDisposable'], 'boolean']
        ];
    }
}

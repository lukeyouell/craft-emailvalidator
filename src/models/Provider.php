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

use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Craft;
use craft\base\Model;

class Provider extends Model
{
    // Public Properties
    // =========================================================================

    public $id;

    public $type;

    public $domain;

    // Public Methods
    // =========================================================================

    public function __toString()
    {
        return (string) $this->domain;
    }

    public function rules()
    {
        return [
            [['type', 'domain'], 'required'],
            [['type'], 'in', 'range' => [ProviderRecord::TYPE_FREE, ProviderRecord::TYPE_DISPOSABLE]],
        ];
    }
}

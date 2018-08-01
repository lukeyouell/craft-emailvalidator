<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\records;

use craft\db\ActiveRecord;

class Provider extends ActiveRecord
{
    // Constants
    // =========================================================================

    const TYPE_FREE = 'free';

    const TYPE_DISPOSABLE = 'disposable';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%ev_providers}}';
    }
}

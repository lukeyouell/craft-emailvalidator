<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\events;

use yii\base\Event;

class ValidationEvent extends Event
{
    // Properties
    // =========================================================================

    public $email;

    public $validation;
}

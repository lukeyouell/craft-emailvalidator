<?php
/**
 * Email Validator plugin for Craft CMS 3.x
 *
 * Email address validation for user registrations, custom forms and more.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\emailvalidator\twigextensions;

use lukeyouell\emailvalidator\EmailValidator;

use Craft;

/**
 * @author    Luke Youell
 * @package   EmailValidator
 * @since     1.0.0
 */
class EmailValidatorTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'EmailValidator';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('someFilter', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('validateEmail', [$this, 'validateEmail']),
        ];
    }

    public function validateEmail($email = null)
    {
        return EmailValidator::getInstance()->emailValidatorService->validateEmail($email);
    }
}

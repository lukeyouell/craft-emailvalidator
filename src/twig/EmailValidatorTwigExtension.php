<?php

namespace lukeyouell\emailvalidator\twig;

use lukeyouell\emailvalidator\helpers\EmailHelper;

class EmailValidatorTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    public function getName()
    {
        return 'EmailValidator';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isValidEmail', [$this, 'isValidEmail']),
        ];
    }

    public function isValidEmail($email = null)
    {
        return EmailHelper::isValid($email);
    }
}

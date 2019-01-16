<?php

namespace lukeyouell\emailvalidator\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class CpBundle extends AssetBundle
{
    public function init()
    {
        // Define the path that your publishable resources live
        $this->sourcePath = '@lukeyouell/emailvalidator/assetbundles';

        // Define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'dist/js/cp.js',
        ];

        $this->css = [
            'dist/css/cp.css',
        ];

        parent::init();
    }
}

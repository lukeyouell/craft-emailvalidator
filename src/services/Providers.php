<?php

namespace lukeyouell\emailvalidator\services;

use lukeyouell\emailvalidator\jobs\UpdateProviders;
use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Craft;
use craft\base\Component;

class Providers extends Component
{
    // Public Methods
    // =========================================================================

    public function updateProviders($type)
    {
        Craft::$app->getQueue()->push(new UpdateProviders([
            'type' => $type,
        ]));
    }

    public function getProviders($type = null)
    {
        $providers = ProviderRecord::find()
            ->limit(50)
            ->all();

        return $providers;
    }

    public function countProviders($type = null)
    {
        $records = ProviderRecord::find();

        if ($type) {
            $records->where(['type' => $type]);
        }

        return $records->count();
    }
}

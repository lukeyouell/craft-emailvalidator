<?php

namespace lukeyouell\emailvalidator\models;

use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use craft\base\Model;

class Provider extends Model
{
    // Public Properties
    // =========================================================================

    public $id;

    public $type;

    public $domain;

    public $enabled = true;

    // Public Properties
    // =========================================================================

    public function rules()
    {
        return [
            ['id', 'integer'],
            [['type', 'domain'], 'string'],
            ['enabled', 'boolean'],
            [['id', 'type', 'domain', 'enabled'], 'required'],
        ];
    }
}

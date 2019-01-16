<?php

namespace lukeyouell\emailvalidator\records;

use lukeyouell\emailvalidator\db\Table;

use craft\db\ActiveRecord;

class Provider extends ActiveRecord
{
    // Constants
    // =========================================================================

    const TYPE_DISPOSABLE = 'disposable';

    const TYPE_FREE = 'free';

    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return Table::PROVIDERS;
    }
}

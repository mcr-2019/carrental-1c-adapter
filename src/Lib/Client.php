<?php

namespace Dataloft\Carrental\Lib;

/**
 * @property string UUID
 * @property string fio
 * @property string email
 */
class Client extends MappingModel
{
    const PASSPORT_TYPE_RUSSIAN = 1;
    const PASSPORT_TYPE_FOREIGN = 0;

    public function getUUID()
    {
        return $this->UUID;
    }
}

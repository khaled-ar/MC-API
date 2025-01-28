<?php

namespace App\Classes\Api\V1;

class UserFilters extends QueryFilter
{
    public function name($name)
    {
        return $this->query->where('fullname', 'like', '%' . $name . '%');
    }
}

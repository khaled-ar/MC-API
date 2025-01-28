<?php

namespace App\Classes\Api\V1;

use Illuminate\Contracts\Database\Query\Builder;

use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $request;

    protected $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function apply(Builder $query)
    {
        $this->query = $query;
        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }
        return $this->query;
    }

    public function filters()
    {
        return $this->request->all();
    }
}

<?php

namespace App\Classes\Api\V1;

use App\Models\Api\V1\Ad;
use App\Traits\Api\V1\Results;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdFilters extends QueryFilter
{
    public function search($name)
    {
        $ads = Ad::where('title', 'LIKE', '%' . $name . '%')->get();
        foreach($ads as $ad) {
            Results::increment($ad->id, 'search_count');
        }

        return $this->query->where('title', 'LIKE', '%' . $name . '%');
    }

    public function price($prices)
    {

        $prices = explode("to", $prices);
        $minPrice = (int) $prices[0];
        $maxPrice = (int) $prices[1];

        return $this->query->where(function ($query) use ($minPrice, $maxPrice) {
            // For ads with 'sale' type
            $query->where(function ($query) use ($minPrice, $maxPrice) {
                $query->where('type', 'sale')->wherebetween('price', [$minPrice, $maxPrice]);
            })

                // For ads with 'buy' type
                ->orWhere(function ($query) use ($minPrice, $maxPrice) {
                    $query->where('type', 'buy')->where(function ($query) use ($minPrice, $maxPrice) {

                        $query->whereBetween(DB::raw('SUBSTRING_INDEX(price, "-", 1)'), [$minPrice, $maxPrice])
                            ->WhereBetween(DB::raw('SUBSTRING_INDEX(price, "-", -1)'), [$minPrice, $maxPrice]);
                    });
                });
        });
    }

    public function tag($name)
    {
        return $this->query->whereRelation('tags', 'name', $name);
    }

    public function type($type)
    {
        return $this->query->where('type', $type);
    }

    public function date($dates)
    {
        $dates = explode("to", $dates);
        $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();

        return $this->query->wherebetween('ads.created_at', [$startDate, $endDate]);
    }

    public function byhighestprice()
    {
        return $this->query->where('type', 'sale')->orderBy(DB::raw("CAST(price AS DECIMAL(10,2))"), 'desc');
    }

    public function bylowestprice()
    {
        return $this->query->where('type', 'sale')->orderBy(DB::raw("CAST(price AS DECIMAL(10,3))"), 'asc');
    }

    public function newest()
    {
        return $this->query->orderByDesc('created_at');
    }

    public function oldest()
    {
        return $this->query->orderBy('created_at', 'asc');
    }

    public function mostviewed()
    {
        return $this->query->join('results', 'ads.id', '=', 'results.ad_id')->select('ads.*')->orderByDesc('view_count');
    }

    public function pinned()
    {
        return $this->query->orderByDesc('pinable');
    }
}

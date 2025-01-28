<?php

namespace App\Classes\Api\V1;

use App\Models\Api\V1\Category;

class Categories {

    // this function return all categories with three sub categories levels

    public static function getThreeLevels() {

        // level 0
        $categories = Category::active()->with( 'children' )->whereNull( 'parent_id' )->get( [ 'id', 'name' ] );
        foreach ( $categories as $category_level_1 ) {
            // level 1
            foreach ( $category_level_1->children as $category_level_2 ) {
                // level 2
                foreach ( $category_level_2->children as $category_level_3 ) {
                    // level 3
                    $category_level_3->children;
                }
            }
        }

        return $categories;
    }

    // this function return all categories with unspecified number of categories levels
    public static function getLevels() {

        $categories = Category::active()->with( 'children' )->whereNull( 'parent_id' )->get( [ 'id', 'name' ] );
        $allwithchildren = [];
        foreach ( $categories as $category ) {
            $allwithchildren = array_merge( $allwithchildren, [ $category, ...$category->getAllDescendants()
            ->whereNull( 'parent_id' ) ] );
        }

        return $allwithchildren;
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Models\Api\V1\Category;
use App\Traits\Api\V1\Images;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    // Get all categories

    public function index()
    {
        // check admin permission
        $this->authorize('viewAny', Category::class);

        $categories = Category::with('admin.user:id,username')->withCount('ads')->get();

        return response()->json([
            'status' => 1,
            'data' => $categories,
        ]);
    }

    // Get only trashed categories

    public function onlyTrashed()
    {
        // check admin permission
        $this->authorize('viewAny', Category::class);

        $categories = Category::onlyTrashed()->with('admin.user:id,username')->withCount('ads')->get();

        return response()->json([
            'status' => 1,
            'data' =>$categories
        ]);
    }

    // Show single category

    public function show(int $id)
    {
        $this->authorize('view', Category::class);

        return response()->json([
            'status' => 1,
            'data' => Category::withTrashed()->with(['ads', 'ads.user', 'admin.user:id,username'])
            ->withCount('ads')
            ->where('id', $id)
            ->get(),
        ]);
    }

    // create a new category [ admin ]

    public function store(StoreCategoryRequest $request)
    {

        // check admin permission
        $this->authorize('create', Category::class);

        return DB::transaction(function () use ($request) {

            $image_name = null;

            if ( isset( $_FILES[ 'image' ] ) ) {

                $image = $_FILES[ 'image' ];
                // changing image name to random string
                $image_name = Images::giveImagesRandomNames( [ $image ] )[ 0 ];

            }

            // create the category
            $category = Category::create(array_merge($request->safe()->all(), [
                'admin_id' => $request->user()->id,
                'image' => $image_name
            ]));

            if (!$category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'عذراً يوجد خطأ ما'
                ]);
            }

            if ($image_name) {
                Images::storeImages([$image], [$image_name], public_path('/categories_icons'));
            }

            return response()->json([
                'status' => 1,
            ]);
        });
    }

    // update exists category [ admin ]

    public function update(UpdateCategoryRequest $request, int $id)
    {
        // check admin permission
        $this->authorize('update', Category::class);

        return DB::transaction(function () use ($request, $id) {

            $category = Category::withTrashed()->where('id', $id)->first();

            // category not found
            if (!$category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'الفئة غير موجودة'
                ]);
            }

            $parent_id = $request->parent_id;
            if($parent_id) {

                $childrens = $category->getAllChildIds()->toArray();
                $childrens = array_merge($childrens,
                Category::whereIn('parent_id', $childrens)
                ->where('status', '<>', 'active')
                ->pluck('id')
                ->toArray());

                if($parent_id == $id || in_array($parent_id, $childrens)) {

                    return response()->json([
                        'status' => 0,
                        'message' => 'لا يمكن ان تكون الفئة هي اساسية وفرعية بالنسبة لنفسها وفي نفس الوقت'
                    ]);
                }
            }

            $current_image = $category->image;

            // handle category icon
            $image_name = null;

            if ( isset( $_FILES[ 'image' ] ) ) {

                $image = $_FILES[ 'image' ];
                // changing image name to random string
                $image_name = Images::giveImagesRandomNames( [ $image ] )[ 0 ];

                $category = $category->update(array_merge(
                    $request->safe()->all(),
                    [
                        'admin_id' => $request->user()->id,
                        'image' => $image_name,
                        'parent_id' => $parent_id
                    ]
                ));

            } else {

                $category = $category->update(array_merge(
                    $request->safe()->all(),
                    [
                        'admin_id' => $request->user()->id,
                        'parent_id' => $parent_id
                    ]
                ));
            }

            if (!$category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'عذراً يوجد خطأ ما'
                ]);
            }

            if ($image_name) {
                // delete old category icon
                Images::deleteImages([$current_image], public_path('/categories_icons'));

                // update category icon
                Images::storeImages([$image], [$image_name], public_path('/categories_icons'));
            }

            return response()->json([
                'status' => 1,
            ]);
        });
    }

    // delete category

    public function delete(int $id)
    {

        // check admin permission
        $this->authorize('delete', Category::class);

        $category = Category::where('id', $id)->first();
        // category not found
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'الفئة غير موجودة'
            ]);
        }

        $childrens = Category::withTrashed()->where('parent_id', $id)->get();

        foreach($childrens as $children) {
            $children->update([
                'parent_id' => null
            ]);
        }

        $category = $category->delete();
        // there is an error
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }

    // restore category

    public function restore(int $id)
    {

        // check admin permission
        $this->authorize('restore', Category::class);

        $category = Category::onlyTrashed()->where('id', $id)->first();
        // category not found
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'الفئة غير موجودة'
            ]);
        }

        $category = $category->restore();
        // there is an error
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }

    // destroy category

    public function destroy(int $id)
    {

        // check admin permission
        $this->authorize('delete', Category::class);

        $category = Category::onlyTrashed()->where('id', $id)->first();
        // category not found
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'الفئة غير موجودة'
            ]);
        }

        $category_image = $category->image;

        $category = $category->forceDelete();
        // there is an error
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'عذراً يوجد خطأ ما'
            ]);
        }

        // delete category icon
        Images::deleteImages([$category_image], public_path('/categories_icons'));

        return response()->json([
            'status' => 1,
        ]);
    }
}

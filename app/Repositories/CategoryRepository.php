<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CategoryRepository
 * @package App\Repositories
 */
class CategoryRepository
{
    /**
     * Retrieve all the categories associated with the current user
     *
     * @return mixed
     */
    public function getAll() {

        return Auth::user()->categories;
    }

    /**
     * Search for categories that has that contains a given name
     *
     * @param $search
     * @return Category[]|\Illuminate\Support\Collection
     */
    public function search($search) {

        $limit = 15;

        $first = Category::where('name', 'like', $search.'%')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $second = new Collection();
        if ($first->count() < $limit) {

            $second = Category::where('name', 'like', '%'.$search.'%')
                ->orderBy('name')
                ->limit($limit - $first->count())
                ->get();
        }

        return $first->merge($second);
    }

    /**
     * Create a category and/or associate it to the current user
     *
     * @param $name
     * @return Category|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function create($name): Category {

        DB::beginTransaction();
        try {

            $category = Category::firstOrCreate(['name'=>$name]);

            $user = Auth::user();
            $exists = $user->categories()->where('categories.id', $category->id)->exists();
            if (!$exists) {
                $user->categories()->attach($category->id);
            }

            DB::commit();

            return $category;

        } catch (\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Exchanges a category to another one and delete it
     * The category is deleted only if:
     * @todo - is not associated with any transaction
     * - does not belongs to any other user
     *
     * @param Category $categoryToDelete
     * @param $exchangeCategoryName
     * @return Category|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function delete(Category $categoryToDelete, $exchange = null) {

        DB::beginTransaction();
        try {

            // @todo check for transactions association first
            Auth::user()->categories()->detach($categoryToDelete);

            $hasUsersAssociate = $categoryToDelete->users()->exists();
            if (!$hasUsersAssociate) {
                if (!$categoryToDelete->is_default) {
                    $categoryToDelete->delete();
                }
            }

            // create and/or associate the exchange category
            $exchangeCategory = ($exchange)? $this->create($exchange) : null;

            DB::commit();

            return $exchangeCategory;

        } catch (\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }
}

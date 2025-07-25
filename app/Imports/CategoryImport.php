<?php

namespace App\Imports;

use App\Models\Category;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class CategoryImport implements ToArray, WithHeadingRow
{
	public function array(array $categories)
	{
		DB::transaction(function () use ($categories) {
			foreach ($categories as $category) {

				if (
					!array_key_exists('name', $category) || !array_key_exists('parent_category', $category)
				) {
					throw new ApiException('Field missing from header.');
				}

				$categoryName = trim($category['name']);
				$categoryCount = Category::where('name', $categoryName)->count();
				if ($categoryCount > 0) {
					throw new ApiException('Category ' . $categoryName . ' Already Exists');
				}

				$parentCategoryName = trim($category['parent_category']);
				$parentCategory = Category::where('name', $parentCategoryName)->first();
				if ($parentCategoryName != "" && !$parentCategory) {
					throw new ApiException('Parent Category ' . $parentCategoryName . ' Not Exists');
				}

				$newCategory = new Category();
				$newCategory->name = $categoryName;
				$newCategory->slug = Str::slug($categoryName, '-');
				$newCategory->parent_id = $parentCategoryName == "" ? null : $parentCategory->id;
				$newCategory->save();
			}
		});
	}
}

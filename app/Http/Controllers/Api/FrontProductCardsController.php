<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\FrontProductCard;
use App\Http\Requests\Api\FrontProductCard\IndexRequest;
use App\Http\Requests\Api\FrontProductCard\StoreRequest;
use App\Http\Requests\Api\FrontProductCard\UpdateRequest;
use App\Http\Requests\Api\FrontProductCard\DeleteRequest;
use App\Models\Product;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Http\Request;

class FrontProductCardsController extends ApiBaseController
{
	protected $model = FrontProductCard::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function storing(FrontProductCard $frontProductCard)
	{
		$request = request();
		$warehouse = warehouse();

		$frontProductCard->products = $this->getIdArrayFromHash($request->products);
		$frontProductCard->warehouse_id = $warehouse->id;

		return $frontProductCard;
	}

	public function updating(FrontProductCard $frontProductCard)
	{

		$request = request();
		$frontProductCard->products = $this->getIdArrayFromHash($request->products);

		return $frontProductCard;
	}

	public function searchProducts(Request $request)
	{
		$searchTerm = $request->search_term;

		$products = Product::select('id', 'name', 'image', 'brand_id', 'category_id')
			->with(['details:id,product_id,sales_price,mrp,tax_id,sales_tax_type', 'details.tax:id,rate', 'brand:id,name,image', 'category:id,name,image'])
			->where('products.name', 'LIKE', "%$searchTerm%");

		if ($request->has('except') && count($request->except) > 0) {
			$products = $products->whereNotIn('products.id', $this->getIdArrayFromHash($request->except));
		}

		$products = $products->take(5)
			->get();

		return ApiResponse::make('Fetched Successfully', ['products' => $products]);
	}
}

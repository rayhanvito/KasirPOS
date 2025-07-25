<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Variation\IndexRequest;
use App\Http\Requests\Api\Variation\StoreRequest;
use App\Http\Requests\Api\Variation\UpdateRequest;
use App\Http\Requests\Api\Variation\DeleteRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Variation;
use Examyou\RestAPI\Exceptions\ApiException;

class VariationController extends ApiBaseController
{
	protected $model = Variation::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

    public function stored(Variation $variation)
    {
        $this->insertOrUpdateVariation($variation);

        return $variation;
    }

    public function updated($variation)
    {
        $this->insertOrUpdatevariation($variation);

        return $variation;
    }

    public function insertOrUpdateVariation($variation){

        $request = request();
        $removedVariations = $request->removed_variations;
        foreach ($removedVariations as $removedVariation){
            $id = $this->getIdFromHash($removedVariation);
            Variation::destroy($id);
        }

        $formFields = $request->value;
        foreach ($formFields as $formField) {

            if ($formField['id'] == '') {
                $exVariation = new Variation();
            } else {
                $id = $this->getIdFromHash($formField['id']);
                $exVariation = Variation::find($id);
            }
            $exVariation->name = $formField['name'];
            $exVariation->parent_id = $variation->id;
            $exVariation->save();

        }
    }

    public function destroying(Variation $variation)
	{
		// Can not delete variation if it is assigned to some product
		$childCategoryCount = ProductVariant::where('variant_id', $variation->id)->count();

		if ($childCategoryCount > 0) {
			throw new ApiException('Variation assigned to some product is not deletable.');
		}

		return $variation;
	}
}


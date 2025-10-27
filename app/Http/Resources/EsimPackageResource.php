<?php

namespace App\Http\Resources;

use App\Models\InAppProduct;
use App\Services\GooglePlayService;
use App\Services\IosService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EsimPackageResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $currency = 'USD';
        if (Auth::guard('api')->check()) {
            $currency = Auth::guard('api')->user()->currency->name;
        }
        $getPrice = packagePrice($this->id, $currency);
        $netPrice = $getPrice['totalAmount'];
        if ($request->fromApp != null) {
            if ($request->fromApp == 'android' || $request->fromApp == 'ios') {
                $iapPrice = packagePrice($this->id, systemflag('iapDefaultCurrency'));
                if ($currency == systemflag('iapDefaultCurrency')) {
                    $netPrice = InAppProduct::where('min_price', '<=', $getPrice['totalAmount'])->where('max_price', '>=', $getPrice['totalAmount'])->pluck('max_price')->first();
                    if ($request->fromApp == 'ios') {
                        $netPrice = InAppProduct::where('min_price', '<=', $getPrice['totalAmount'])->where('max_price', '>=', $getPrice['totalAmount'])->pluck('set_price')->first();
                    }
                } elseif ($request->fromApp == 'ios') {
                    $iapProductSku = InAppProduct::where('min_price', '<=', $iapPrice)->where('max_price', '>=', $iapPrice)->pluck('sku')->first();
                    $applePlayService = new IosService($iapProductSku, 'USA');
                    $netPrice = $applePlayService->priceGet();
                } elseif ($request->fromApp == 'android') {
                    $iapProductSku = InAppProduct::where('min_price', '<=', $iapPrice)->where('max_price', '>=', $iapPrice)->pluck('sku')->first();
                    $googlePlayService = new GooglePlayService($iapProductSku, $currency);
                    $netPrice = $googlePlayService->priceGet();
                }
            }
        }
        return [
            'id' => $this->id,
            'operator_id' => $this->operator_id,
            'airalo_package_id' => $this->airalo_package_id,
            'name' => $this->name,
            'type' => $this->type,
            'day' => $this->day,
            'is_unlimited' => $this->is_unlimited,
            'short_info' => $this->short_info,
            'data' => $this->data,
            'net_price' => (Double)$netPrice,
            'country' => $this->operator->country ?? null,
            'region' => $this->operator->region ?? null,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

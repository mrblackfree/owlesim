<?php

namespace App\Http\Resources;

use App\Models\InAppProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\GooglePlayService;
use App\Services\IosService;

class PackageDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currency = Auth::guard('api')->check() ? Auth::guard('api')->user()->currency->name : 'USD';
        $fromApp = $request->input('fromApp'); // safer than $request->fromApp
        $defaultCurrency = systemflag('iapDefaultCurrency') ?? 'USD';

        $packagePrice = packagePrice($this->id, $currency);
        $netPrice = $packagePrice['totalAmount'] ?? 0;

        if (in_array($fromApp, ['ios', 'android'])) {
            $iapPrice = packagePrice($this->id, $defaultCurrency)['totalAmount'] ?? 0;

            $iapProduct = InAppProduct::where('min_price', '<=', $iapPrice)
                ->where('max_price', '>=', $iapPrice)
                ->first();

            if ($iapProduct) {
                if ($currency === $defaultCurrency) {
                    $netPrice = $fromApp === 'ios' ? $iapProduct->set_price : $iapProduct->max_price;
                } else {
                    $sku = $iapProduct->sku;
                    $cacheKey = "iap_price_{$fromApp}_{$sku}_{$currency}";

                    // Cache API prices for 10 minutes
                    $netPrice = Cache::remember($cacheKey, 600, function () use ($fromApp, $sku, $currency) {
                        if ($fromApp === 'ios') {
                            return (new IosService($sku, 'USA'))->priceGet();
                        } elseif ($fromApp === 'android') {
                            return (new GooglePlayService($sku, $currency))->priceGet();
                        }
                        return 0;
                    });
                }
            }
        }

        return [
            "id" => $this->id,
            "operator_id" => $this->operator_id,
            "airalo_package_id" => $this->airalo_package_id,
            "name" => $this->name,
            "type" => $this->type,
            "day" => $this->day,
            "is_unlimited" => (bool) $this->is_unlimited,
            "short_info" => $this->short_info,
            "net_price" => (Double)$netPrice,
            "data" => $this->data,
            "price" => $this->price,
            "is_active" => (bool) $this->is_active,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "is_fair_usage_policy" => (bool) $this->is_fair_usage_policy,
            "fair_usage_policy" => $this->fair_usage_policy,
            "qr_installation" => $this->qr_installation,
            "manual_installation" => $this->manual_installation,
            "country" => $this->country,
            "operator" => $this->operator
        ];
    }
}

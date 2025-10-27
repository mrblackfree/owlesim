<?php

use App\Models\EmailTemplate;
use App\Models\EsimPackage;
use App\Models\Systemflag;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


if (!function_exists('systemflag')) {
    function systemflag($flagName)
    {
        $systemflag = Systemflag::where('name', @$flagName)->pluck('value')->first();
        if (@$systemflag) {
            return $systemflag;
        }
        return false;
    }
}
if (!function_exists('emailTemplate')) {
    function emailTemplate($tempName)
    {
        $tempData = EmailTemplate::where('name', @$tempName)->first();
        if (@$tempData) {
            return $tempData;
        }
        return false;
    }
}
if (!function_exists('packagePrice')) {
    function packagePrice($packageid,$currency)
    {
        $adminInclude = systemflag('PackageCommission');
        $packagePrices = EsimPackage::where('id', $packageid)->pluck('prices')->first();
        if (!$packagePrices) {
            return false;
        }
        $userCurrency = $currency;

        $airaloPrice = $packagePrices['net_price'][$userCurrency] ?? 0;
        if ($adminInclude == 0) {
            return ['totalAmount' => $airaloPrice, 'airaloPrice' => $airaloPrice];
        }
        $includePrice = $airaloPrice * $adminInclude / 100;
        $netPrice = $includePrice + $airaloPrice;
        if($userCurrency == 'INR'){
            $netPrice = round($netPrice);
        }
        return ['totalAmount' => $netPrice ?? 0, 'airaloPrice' => $airaloPrice ?? 0];
    }
}

if (!function_exists('updateIfChanged')) {
    /**
     * Update model only if attributes changed.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attrs
     * @return bool True if model was updated
     */
    function updateIfChanged($model, array $attrs): bool
    {
        $dirty = false;

        foreach ($attrs as $key => $value) {
            if ($model->$key != $value) {
                $model->$key = $value;
                $dirty = true;
            }
        }

        if ($dirty) {
            $model->save();
        }

        return $dirty;
    }
}


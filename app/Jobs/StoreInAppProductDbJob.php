<?php

namespace App\Jobs;

use App\Models\Currency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\InAppProduct;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreInAppProductDbJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $currency;
    protected $priceGap;
    protected $slotsCount;

    public function __construct($currency, $priceGap, $slotsCount)
    {
        $this->currency = $currency;
        $this->priceGap = $priceGap;
        $this->slotsCount = $slotsCount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $getCurrency = Currency::findOrFail($this->currency);

        $last = InAppProduct::orderBy('id', 'desc')->first();
        $baseStart = ($last && isset($last->max_price)) ? ($last->max_price + 1) : 0;
        $gap = $this->priceGap;
        $count = $this->slotsCount;

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $min = $baseStart + $i * $gap;
            $max = $min + ($gap - 1);
            $rows[] = [
                'currency_id' => $getCurrency->id,
                'name' => "range_between_{$min}_to_{$max}",
                'sku' => "range_between_{$min}_to_{$max}",
                'min_price' => $min,
                'max_price' => $max,
                'set_price' => $max,
                'isActive' => 1,
                'isAndroidUpload' => 0,
                'isAppleUpload' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        InAppProduct::insert($rows);
    }
}

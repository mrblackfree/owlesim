<?php

namespace App\Jobs;

use App\Models\InAppProduct;
use Google_Client;
use Google_Service_AndroidPublisher;
use Google_Service_AndroidPublisher_InAppProduct;
use Google_Service_AndroidPublisher_Price;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateInAppProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $sku;
    protected string $title;
    protected string $description;
    protected float $price;
    protected string $currency;

    /**
     * Create a new job instance.
     */
    public function __construct(string $sku, string $title, string $description, float $price, string $currency = 'INR')
    {
        $this->sku = strtolower(str_replace('-', '_', $sku));
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->currency = $currency;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $androidPackageName = systemflag('androidPackageName');
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/google/esim-firebase.json'));
            $client->addScope(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);
            $client->setAccessType('offline');

            $service = new \Google_Service_AndroidPublisher($client);

            $price = (float) $this->price;
            $priceMicros = bcmul((string) $price, '1000000');

            $defaultPrice = new \Google_Service_AndroidPublisher_Price();
            $defaultPrice->setCurrency($this->currency);
            $defaultPrice->setPriceMicros($priceMicros);

            $inAppProduct = new \Google_Service_AndroidPublisher_InAppProduct([
                'packageName'     => $androidPackageName,
                'sku'             => $this->sku,
                'status'          => 'active',
                'defaultLanguage' => 'en-GB',
                'listings'        => [
                    'en-GB' => [
                        'title'       => $this->title,
                        'description' => $this->description,
                    ],
                ],
                'defaultPrice'    => $defaultPrice,
                'purchaseType'    => 'managedUser',
            ]);
            Log::info('Preparing in-app product', [
                'sku' => $this->sku,
                'price' => $this->price,
                'priceMicros' => $priceMicros,
                'currency' => $this->currency
            ]);
            if ($this->checkIfProductExists($service, $this->sku)) {
                // Update
                $response = $service->inappproducts->update(
                    $androidPackageName,
                    $this->sku,
                    $inAppProduct,
                    ['autoConvertMissingPrices' => true]
                );
                Log::info("In-app product updated", ['sku' => $this->sku, 'response' => $response]);
            } else {
                // Insert
                $response = $service->inappproducts->insert(
                    $androidPackageName,
                    $inAppProduct,
                    ['autoConvertMissingPrices' => true]
                );
                Log::info("In-app product created", ['sku' => $this->sku, 'response' => $response]);
            }
            InAppProduct::where('sku',$this->sku)->update(['isAndroidUpload' => 1]);
        } catch (\Google\Service\Exception $e) {
            $error = json_decode($e->getMessage(), true);
            Log::error("Google API Error", [
                'sku' => $this->sku,
                'message' => $error['error']['message'] ?? $e->getMessage(),
                'details' => $error
            ]);
        } catch (\Exception $e) {
            Log::error("General Error while creating in-app product", [
                'sku' => $this->sku,
                'message' => $e->getMessage()
            ]);
        }
    }
    private function checkIfProductExists($service, $sku): bool
    {
        try {
            $androidPackageName = systemflag('androidPackageName');
            $product = $service->inappproducts->get($androidPackageName, $sku);
            return !empty($product);
        } catch (\Google\Service\Exception $e) {
            // If Google returns 404 → product does not exist
            if ($e->getCode() !== null && $e->getCode() === 404) {
                return false;
            }
            throw $e; // rethrow other errors
        }
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\AppleStoreIapJob;
use App\Models\Country;
use App\Models\EsimPackage;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Region;
use App\Services\DynamicPriceImageGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Google_Client;
use Google_Service_AndroidPublisher;
use Illuminate\Support\Facades\Http;


class HomeController extends Controller
{
    public function home(Request $request)
    {
        try {
            $destinations = Country::whereHas('operators.esimPackages')
                ->with(['operators' => function ($q) {
                    $q->whereHas('esimPackages')
                        ->with('esimPackages');
                }])
                ->inRandomOrder()
                ->take(6)
                ->get();

            $esimPackages = EsimPackage::where('type', 'sim')
                ->whereHas('operator', function ($query) {
                    $query->where('plan_type', 'data-voice-text');
                })
                ->with('operator.country')
                ->inRandomOrder()
                ->take(3)
                ->get();
            $faqs = Faq::get();
            return view('frontend.index', compact('faqs', 'destinations', 'esimPackages'));
        } catch (\Exception $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $countries = Country::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'image', 'country_code', DB::raw("'Rountry' as type"))
            ->limit(5)
            ->get();

        $regions = Region::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'image', DB::raw("null as country_code"), DB::raw("'Region' as type"))
            ->limit(5)
            ->get();

        $results = $countries->merge($regions);

        return response()->json($results);
    }

    public function testFunction(DynamicPriceImageGenerator $generator)
    {
        $prices = 250;
        $templatePath = storage_path('app/templates/review_image.png');

        $images = $generator->generatePriceImageWithGD($templatePath, $prices);

        dd($images);
        // $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IlBSQUFYNzhVNE4ifQ.eyJpc3MiOiI2OGU3ZDZiNi1jMzdjLTQ5MmMtOTM2MC0zOGNkMjAxOTQ2ODQiLCJpYXQiOjE3NTk1NTQ5MzQsImV4cCI6MTc1OTU1NjEzNCwiYXVkIjoiYXBwc3RvcmVjb25uZWN0LXYxIiwiYmlkIjoiY29tLmVzaW10ZWwuYXBwIn0.Scc5sCuBYkfz4e0xCpqmV0dkIA2wG-qaGx54tNwF77I0E17OZZAOAY8UzF8pYpPPzMPk39TJjLZY7dONA2uY4A';
        // $imagePath = storage_path('app/public/review_screenshots/iap_500_1759496756.png');

        // $iapId = 6752902214;
        // $response = $this->storeReviewScreeshoot($jwt, $imagePath, $iapId);
        // return $response;


        // dd($product['prices']['AE']['priceMicros']);
    }

    private function storeReviewScreeshoot($jwt, $imagePath, $iapId)
    {
        // Step 1: Reserve screenshot slot on App Store Connect
        $reserveResponse = Http::withToken($jwt)
            ->post('https://api.appstoreconnect.apple.com/v1/inAppPurchaseAppStoreReviewScreenshots', [
                'data' => [
                    'type' => 'inAppPurchaseAppStoreReviewScreenshots',
                    'attributes' => [
                        'fileName' => basename($imagePath),
                        'fileSize' => filesize($imagePath),
                    ],
                    'relationships' => [
                        'inAppPurchaseV2' => [
                            'data' => [
                                'type' => 'inAppPurchases',
                                'id' => $iapId,
                            ],
                        ],
                    ],
                ],
            ]);

        if (!$reserveResponse->successful()) {
            throw new \Exception('Failed to reserve screenshot: ' . $reserveResponse->body());
        }

        $data = $reserveResponse->json('data.attributes.uploadOperations.0');
        $uploadUrl = $data['url'] ?? null;
        if (!$uploadUrl) {
            throw new \Exception('Upload URL missing in response.');
        }

        // Step 2: Upload binary image to S3
        $fileContents = file_get_contents($imagePath);
        $contentType = mime_content_type($imagePath) ?: 'application/octet-stream';

        $uploadResponse = Http::withHeaders([
            'Content-Type' => $contentType,
        ])->withBody($fileContents, $contentType)
            ->put($uploadUrl);

        if (!$uploadResponse->successful()) {
            throw new \Exception('Upload failed: ' . $uploadResponse->body());
        }

        // Step 3: Commit the upload
        $screenshotId = $reserveResponse->json('data.id');
        $commitResponse = Http::withToken($jwt)
            ->patch("https://api.appstoreconnect.apple.com/v1/inAppPurchaseAppStoreReviewScreenshots/{$screenshotId}", [
                'data' => [
                    'type' => 'inAppPurchaseAppStoreReviewScreenshots',
                    'id' => $screenshotId,
                    'attributes' => [
                        'uploaded' => true,
                    ],
                ],
            ]);

        return [
            'reserve' => $reserveResponse->json(),
            'upload' => $uploadResponse->status(),
            'commit' => $commitResponse->json(),
        ];
    }
}

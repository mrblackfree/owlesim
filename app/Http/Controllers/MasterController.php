<?php

namespace App\Http\Controllers;

use App\Jobs\BatchUpdateInAppProductsJob;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Country;
use App\Models\Currency;
use App\Models\EsimPackage;
use App\Models\Page;
use App\Models\Region;
use App\Services\AiraloService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\InAppProduct;
use Illuminate\Http\Request;

class MasterController extends BaseController
{
    public function currencies()
    {
        try {
            $currencies = Currency::where('is_active', 1)->select('id', 'name', 'symbol')->get();
            return $this->sendResponse($currencies, 'Currencies data fetched');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
     public function countries(Request $request)
    {
        try {
            $countries = Country::where('is_active', true)->get();
             $currency = 'USD';
            if (Auth::guard('api')->check()) {
                $currency = Auth::guard('api')->user()->currency->name;
            }
            $countries->map(function ($country) use ($request,$currency) {
                $allPackages = $country->operators->flatMap->esimPackages;

                $prices = $allPackages->map(function ($pkg) use ($request,$currency) {
                    $getPrice = packagePrice($pkg->id,$currency);
                    $netPrice = $getPrice['totalAmount'];
                    if (isset($request->fromApp)) {
                        if ($request->fromApp == 'android' || $request->fromApp == 'ios') {
                            $netPrice = InAppProduct::where('min_price', '<=', $getPrice['totalAmount'])->where('max_price', '>=', $getPrice['totalAmount'])->pluck('max_price')->first();
                        }
                    }
                    return $netPrice;
                })->filter();
                $country->start_price = $prices->min();
                unset($country->operators);

                return $country;
            });

            return $this->sendResponse($countries, 'Country data fetched');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function regions(Request $request)
    {
        try {
            $regions = Region::where('is_active', true)->get();
             $currency = 'USD';
            if (Auth::guard('api')->check()) {
                $currency = Auth::guard('api')->user()->currency->name;
            }
            $regions->map(function ($region) use ($request,$currency) {
                $allPackages = $region->operators->flatMap->esimPackages;

                // Calculate prices for each package
                $prices = $allPackages->map(function ($pkg) use ($request,$currency) {
                    $getPrice = packagePrice($pkg->id,$currency);
                     $netPrice = $getPrice['totalAmount'];
                    if (isset($request->fromApp)) {
                        if ($request->fromApp == 'android' || $request->fromApp == 'ios') {
                            $netPrice = InAppProduct::where('min_price', '<=', $getPrice['totalAmount'])->where('max_price', '>=', $getPrice['totalAmount'])->pluck('max_price')->first();
                        }
                    }
                    return $netPrice;
                })->filter();
                $region->start_price = $prices->min();
                unset($region->operators);

                return $region;
            });
            return $this->sendResponse($regions, 'Region data fetched');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function pages()
    {
        try {
            $pages = Page::where('is_active', true)->get();
            return $this->sendResponse($pages, 'Pages data fetched successfully');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function blogs()
    {
        try {
            $blogs = Blog::where('is_published', true)->get();
            return $this->sendResponse($blogs, 'Blogs Data fetched successfully');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function banners()
    {
        try {
            $banners = Banner::where('is_active', true)->whereDate('banner_to', '>', Carbon::now())->get();
            return $this->sendResponse($banners, 'Data fetched successfully');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function deviceCompatibleEsim(AiraloService $airalo)
    {
        try {
            $response = $airalo->deviceCompatible();
            return $this->sendResponse($response['data'], 'Data fetched successfully');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }

       public function generalSettings()
    {
        try {
            $response = [
                'logo'    => systemflag('logo'),
                'favicon' => systemflag('favicon'),
                'webconfig' => [
                    'siteName'          => systemflag('appName'),
                    // Firebase
                    'firebaseApiKey'        => systemflag('firebaseApiKey'),
                    'firebaseAuthDomain'    => systemflag('firebaseAuthDomain'),
                    'firebaseProjectId'     => systemflag('firebaseProjectId'),
                    'firebaseStorageBucket' => systemflag('firebaseStorageBucket'),
                    'firebaseSenderId'      => systemflag('firebaseSenderId'),
                    'firebaseAppId'         => systemflag('firebaseAppId'),
                    'firebaseVapidKey'      => systemflag('firebaseVapidKey'),

                    // Other configs
                    'webBaseUrl'        => systemflag('webBaseUrl'),
                    'contactEmail'      => systemflag('contactEmail'),
                    'contactPhone'      => systemflag('contactPhone'),
                    'address'           => systemflag('address'),
                ],
            ];
            return $this->sendResponse($response, 'Data fetched successfully');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
}

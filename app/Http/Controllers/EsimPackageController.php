<?php

namespace App\Http\Controllers;

use App\Http\Resources\EsimPackageResource;
use App\Http\Resources\PackageDetailResource;
use Illuminate\Http\Request;
use App\Models\EsimPackage;
use App\Services\AiraloService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EsimPackageController extends BaseController
{
    public function index(Request $request, AiraloService $airalo)
    {
        try {
            $query = EsimPackage::with(['operator.country', 'operator.region', 'operator.region.countries'])
                ->where('is_active', true)
                ->select(
                    'id',
                    'operator_id',
                    'airalo_package_id',
                    'name',
                    'type',
                    'day',
                    'is_unlimited',
                    'short_info',
                    'net_price',
                    'data',
                    'price',
                    'is_active',
                    'is_popular',
                    'created_at',
                    'updated_at',
                    'prices'
                );

            // Type filter + TopUp handling
            if ($type = $request->input('type')) {
                if ($type === 'topup' && $request->filled('iccid')) {
                    $topupLists = $airalo->getTopUp($request->iccid);
                    $packageIds = collect($topupLists['data'])->pluck('id')->toArray();

                    $query->where('type', $type)
                        ->whereIn('airalo_package_id', $packageIds);
                } else {
                    $query->where('type', $type);
                }
            } else {
                $query->where('type', 'sim');
            }

            // Validity filter
            if ($request->filled('validity_days')) {
                $query->where('day', $request->input('validity_days'));
            }

            // Unlimited filter (use !== null so 0 is respected)
            if (!is_null($request->input('is_unlimited'))) {
                $query->where('is_unlimited', $request->input('is_unlimited'));
            }

            // Country filter
            if ($request->filled('country')) {
                $query->whereHas(
                    'operator',
                    fn($q) =>
                    $q->where('country_id', $request->input('country'))
                );
            }
            if ($request->filled('slug')) {
                $query->whereHas('operator', function ($q) use ($request) {
                    $q->whereHas('country', function ($query) use ($request) {
                        $query->where('slug', $request->input('slug'));
                    })->orWhereHas('region', function ($query) use ($request) {
                        $query->where('slug', $request->input('slug'));
                    });
                });
            }

            // Region filter
            if ($request->filled('region_id')) {
                $query->whereHas(
                    'operator',
                    fn($q) =>
                    $q->where('region_id', $request->input('region_id'))
                );
            }

            // Plan type filters
            if ($request->boolean('data_pack')) {
                $query->whereHas('operator', fn($q) => $q->where('plan_type', 'data'));
            }
            if ($request->boolean('text_voice')) {
                $query->whereHas('operator', fn($q) => $q->where('plan_type', 'data-voice-text'));
            }

            // Flags
            if (!is_null($request->input('is_popular'))) {
                $query->where('is_popular', $request->input('is_popular'));
            }
            if (!is_null($request->input('is_recommend'))) {
                $query->where('is_recommend', $request->input('is_recommend'));
            }
            if (!is_null($request->input('is_best_value'))) {
                $query->where('is_best_value', $request->input('is_best_value'));
            }

            // Sort by price
            if ($request->filled('sort_price')) {
                if ($request->input('sort_price') === 'high') {
                    $query->orderByDesc('net_price');
                } elseif ($request->input('sort_price') === 'low') {
                    $query->orderBy('net_price', 'asc');
                }
            }

            // Default ordering
            $query->orderByDesc('is_popular')
                ->orderByDesc('is_recommend')
                ->orderByDesc('is_best_value')
                ->orderByDesc('id');

            // Pagination (default 3 if not provided)
            $perPage = $request->input('per_page', 3);
            $packages = $query->paginate($perPage);
            return EsimPackageResource::collection($packages)->additional([
                'success' => true,
                'message' => 'Packages retrieved successfully.',
            ]);
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve packages', 500, ['error' => $e->getMessage()]);
        }
    }


    // Get package details
    public function show($id)
    {
        try {
            $package = EsimPackage::with('country', 'operator')->select('id', 'operator_id', 'airalo_package_id', 'name', 'type', 'day', 'is_unlimited', 'short_info', 'net_price', 'data', 'price', 'is_active', 'created_at', 'updated_at', 'prices', 'is_fair_usage_policy', 'fair_usage_policy', 'qr_installation', 'manual_installation')->where('is_active', true)->findOrFail($id);
            return response()->json([
                'data' => new PackageDetailResource($package),
                'success' => true,
                'message' => 'Package retrieved successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Package not found', 404);
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve package', 500, ['error' => $e->getMessage()]);
        }
    }
}

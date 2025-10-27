<?php

namespace App\Http\Controllers;

use Airalo\Airalo;
use App\Http\Resources\GetUsageResource;
use App\Models\UserEsim;
use App\Models\UserNotification;
use App\Services\AiraloService;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    public function getUsage(Request $request, AiraloService $airalo)
    {
        try {
            $mysims = $request->user()->esims;
            $data = [];

            foreach ($mysims as $sim) {
                if ($sim->status == 'ACTIVE') {
                    $response = $airalo->getUsage($sim->iccid);
                    if ($sim->package->operator->type == 'local') {
                        $location = $sim->package->operator->country;
                    } else {
                        $location = $sim->package->operator->region;
                    }
                    $data[] = [
                        'id' => $sim->id,
                        'iccid'  => $sim->iccid,
                        'esim_status' => $sim->status,
                        'location' => $location,
                        'usage'  => $response['data'] ?? null
                    ];
                }
            }

            return $this->sendResponse($data, 'Data retrieved successfully!');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function notifications(Request $request)
    {
        try {
            $userId = $request->user()->id;
            if ($request->is_read) {
                UserNotification::where('user_id', $userId)->update(['is_read' => 1]);
            }
            if ($request->is_read && $request->notification_id) {
                UserNotification::where('id', $request->notification_id)->update(['is_read' => 1]);
            }
            $perPage = $request->input('per_page', 10);
            $notifications = UserNotification::where('user_id', $userId)->orderBy('id', 'desc')->paginate($perPage);
            return $this->sendResponse($notifications, 'Data retrieved successfully!');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
    public function esimInstruction(Request $request, AiraloService $airalo)
    {
        try {
            $user = $request->user();
            if (!$user->esims->where('iccid', $request->iccid)) {
                return $this->sendError('Iccid id invalid');
            }
            $request->validate([
                'iccid' => 'required|numeric|exists:user_esims,iccid',
            ]);
            $iccid = $request->iccid;
            $response = $airalo->instructions($iccid);
            return $this->sendResponse($response['data'], 'Instruction retrieved successfully!');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
}

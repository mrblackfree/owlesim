<?php

namespace App\Jobs;

use App\Models\UserEsim;
use App\Models\UserNotification;
use App\Notifications\ActivatedSimNoti;
use App\Notifications\OrderPlacedNoti;
use App\Services\AiraloService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserEsimActivation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $esimId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $esimId)
    {
        $this->esimId = $esimId;
    }

    /**
     * Execute the job.
     */
    public function handle(AiraloService $airaloService)
    {
        try {
            $esim = UserEsim::find($this->esimId);

            if (!$esim) {
                return;
            }

            $iccid = $esim->iccid;
            $data = $airaloService->getEsimDetails($iccid);

            if (!empty($data['data'])) {
                foreach ($data['data'] as $response) {
                    $esim->update([
                        'status'       => $response['status'] ?? null,
                        'activated_at' => $response['activated_at'] ?? null,
                        'expired_at'   => $response['expired_at'] ?? null,
                        'finished_at'  => $response['finished_at'] ?? null,
                    ]);

                    // Send notification only when status changes to ACTIVE
                    if (!$esim->activation_notified) {
                        $esim->user->notify(new ActivatedSimNoti($esim));

                        UserNotification::create([
                            'user_id'    => $esim->user_id,
                            'title'      => 'Esim Activated!',
                            'type'       => 2,
                            'description' => 'Your ICCID ' . $esim->iccid . ' is now active!',
                        ]);

                        // Mark as notified
                        $esim->activation_notified = true;
                        $esim->save();
                    }
                }
            } else {
                $orderCode = $esim->order->activation_details['id'];
                $data = $airaloService->getOrderStatus($orderCode);
                $esim->status = $data['data']['status']['name'] ?? $esim->status;
                $esim->save();
            }
        } catch (\Exception $e) {
            Log::error('UpdateUserEsimActivation failed', [
                'error' => $e->getMessage(),
                'user_esim_id' => $esim->id ?? null,
            ]);
        }
    }
}

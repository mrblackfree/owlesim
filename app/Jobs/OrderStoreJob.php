<?php

namespace App\Jobs;

use App\Mail\AllMail;
use App\Models\EmailTemplate;
use App\Models\UserEsim;
use App\Models\EsimOrder;
use App\Models\UserNotification;
use App\Notifications\EsimReadyToUseNoti;
use App\Notifications\OrderPlacedNoti;
use App\Services\AiraloService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $AiraloPackageId;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId, $AiraloPackageId, $user)
    {
        $this->orderId = $orderId;
        $this->AiraloPackageId = $AiraloPackageId;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $updateOrder = EsimOrder::find($this->orderId);
        try {
            // $airalo = new AiraloService();

            // $activation = $airalo->placeOrder(
            //     $this->AiraloPackageId,
            //     [
            //         'name'     => $this->user->name ?? '',
            //         'email'    => $this->user->email ?? '',
            //         'quantity' => 1
            //     ]
            // );
            // if ($activation) {
            //     if ($activation['meta']['message'] == 'success') {
                    $updateOrder->status = 'Completed';
                    $updateOrder->activation_details = null;//$activation['data'];
                    $updateOrder->save();

                    // foreach ($activation['data']['sims'] as $esim) {
                    //     $esim = UserEsim::create([
                    //         'user_id' => $this->user->id,
                    //         'order_id' => $updateOrder->id,
                    //         'package_id' => $updateOrder->esim_package_id,
                    //         'iccid' => $esim['iccid'] ?? null,
                    //         'imsis' => $esim['imsis'] ?? null,
                    //         'matching_id' => $esim['matching_id'] ?? null,
                    //         'qrcode' => $esim['qrcode'] ?? null,
                    //         'qrcode_url' => $esim['qrcode_url'] ?? null,
                    //         'airalo_code' => $esim['airalo_code'] ?? null,
                    //         'apn_type' => $esim['apn_type'] ?? null,
                    //         'apn_value' => $esim['apn_value'] ?? null,
                    //         'is_roaming' => $esim['is_roaming'] ?? null,
                    //         'confirmation_code' => $esim['confirmation_code'] ?? null,
                    //         'apn' => $esim['apn'] ?? null,
                    //         'direct_apple_installation_url' => $esim['direct_apple_installation_url'] ?? null,
                    //     ]);
                    //     $esim->user->notify(new EsimReadyToUseNoti($esim['iccid']));
                    // }

                    $orderTemp = emailTemplate('activation');
                    $companyName = systemflag('appName');
                    $template = $orderTemp->description;
                    $tempSubject = $orderTemp->subject;
                    $location = '';
                    if ($updateOrder->package->operator->type == 'local') {
                        $location = $updateOrder->package->operator->country->name ?? '';
                    } else {
                        $location = $updateOrder->package->operator->region->name ?? '';
                    }
                    $data = [
                        'qrCode' =>  $updateOrder->order_ref,
                        'packageName' =>  $updateOrder->package->name,
                        'location' =>  $location,
                        'iccid' =>  $updateOrder->esims->iccid ?? '',
                        'activationUrl' =>  $updateOrder->esims->qrcode ?? '',
                        'companyName' => $companyName,
                        'date' => date('Y')
                    ];

                    Mail::to($updateOrder->user->email)->send(new AllMail($template, $data, $tempSubject));
                // } else {
                //     $updateOrder->status = 'failed';
                //     $updateOrder->activation_details = $activation['data'];
                // }
            // }
        } catch (\Throwable $e) {
            $updateOrder->status = 'failed';
            $updateOrder->activation_details = $e->getMessage();
            Log::error('OrderStoreJob Failed: ' . $e->getMessage(), [
                'order_id' => $this->orderId ?? null,
                'user_id' => $this->user->id ?? null
            ]);
        }
    }
}

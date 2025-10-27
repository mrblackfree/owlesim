<?php

namespace App\Console\Commands;

use App\Jobs\UpdateUserEsimActivation;
use App\Models\UserEsim;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateEsimActivationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-esim-activation-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Esim Activation Status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UserEsim::where('status', 'NOT_ACTIVE')
            ->orWhere(function ($q) {
                $q->whereNotNull('expired_at')
                    ->where('expired_at', '<=', Carbon::now());
            })
            ->chunk(20, function ($esims) {
                foreach ($esims as $esim) {
                    UpdateUserEsimActivation::dispatch($esim->id);
                }
            });
    }
}

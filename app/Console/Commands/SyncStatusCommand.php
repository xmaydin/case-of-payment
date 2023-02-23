<?php

namespace App\Console\Commands;

use App\Jobs\SyncSubsStatusJob;
use App\Models\Subscription;
use App\Repository\Eloquent\PaymentServiceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SyncStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Subscription::where([
            'status' => 'active'
        ])->chunk(2000, function ($subs) {
            $jobs = [];
            foreach ($subs as $sub) {
                $jobs[] = new SyncSubsStatusJob($sub);
            }
            Bus::batch($jobs)
                ->onQueue('subscription')
                ->onConnection('redis')
                ->dispatch();
        });
    }
}

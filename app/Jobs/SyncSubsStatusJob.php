<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Repository\Eloquent\PaymentServiceRepository;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSubsStatusJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscription;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentServiceRepository $paymentServiceRepository): void
    {
        $paymentServiceRepository->subscriptionStatus($this->subscription);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\Table;

class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily';

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
        $date       = Carbon::now()->subDay();
        $startDate  = $date->copy()->startOfDay();
        $endDate    = $date->copy()->endOfDay();

        $subs = Subscription::select('status', DB::raw('count(*) as total'))
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get()->toArray();

        $rows = [...$subs];

        $table = new Table($this->output);
        $table->setHeaders(['Status', 'Total']);
        $table->setRows($rows);
        $table->render();
    }
}

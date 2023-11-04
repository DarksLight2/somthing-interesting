<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Log;
use Bus;
use Throwable;
use SplFileInfo;
use Carbon\Carbon;
use App\Jobs\RemoveFileJob;
use Illuminate\Console\Command;

final class DumpCleaner extends Command
{
    protected $signature = "dump:clear {--days= : The number of days must pass for the dump to be deleted}";
    protected $description = "Clear database dumps per days";

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        $collection = collect();

        $this->info('Started clearing dumps...');

        foreach (glob(config('database.dump.path') . '/*') as $path) {
            $file = new SplFileInfo($path);
            if(now()->diffInDays(Carbon::createFromTimestamp($file->getATime())) >= $this->option('days')) {
                $collection->push(new RemoveFileJob($path));
            }
        }

        Bus::batch($collection)->dispatch();

        $this->info("Finish. {$collection->count()} dumps must be removed.");

        if (($amount = $collection->count()) > 0) {
            Log::channel('daily')->info("Количество дампов базы данных которые удалены $amount");
        }

        return self::SUCCESS;
    }
}

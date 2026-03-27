<?php

namespace App\Console\Commands;

use App\Models\Youth;
use Illuminate\Console\Command;

class ArchiveOverAgeYouth extends Command
{
    protected $signature = 'youth:archive-over-age';

    protected $description = 'Archive youth profiles that reached age 31 and above';

    public function handle()
    {
        $count = Youth::whereDate('birthday', '<=', now()->subYears(31))
            ->where('is_archived', 0)
            ->update(['is_archived' => 1]);

        $this->info("$count youth profiles archived.");
    }
}

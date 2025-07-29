<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckFaviconPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:favicon-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the generated path for favicon.png';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = 'uploads/logo/favicon.png';

        $this->info('Using asset(Storage::url()):');
        $this->info(asset(Storage::url($path)));

        $this->info('Using asset("storage/" . $path):');
        $this->info(asset("storage/" . $path));

        return Command::SUCCESS;
    }
}

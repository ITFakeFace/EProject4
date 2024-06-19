<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ResetDayOfLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset day of leave to 0 of all Staff';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('http://localhost:8888/staff/resetDayOfLeave');
        $body = json_decode($response->body(), true);
        $this->info('ResetDayOfLeave:Cron Cummand Run successfully!');
    }
}

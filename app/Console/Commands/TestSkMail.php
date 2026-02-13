<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SKCreated;
use App\Models\User;

class TestSkMail extends Command
{
    protected $signature = 'mail:test-sk {to}';
    protected $description = 'Send a test SKCreated mail to an address using first admin credentials/permissions';

    public function handle()
    {
        $to = $this->argument('to');

        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            $this->error('No admin user found');
            return 1;
        }

        $dummy = new User(['name' => 'SK Test', 'email' => $to, 'barangay' => 'Test Barangay']);
        $password = 'test-password';

        try {
            Mail::to($to)->send(new SKCreated($dummy, $password));
            $this->info("Mail sent to {$to}");
            return 0;
        } catch (\Exception $e) {
            $this->error('Mail test failed: ' . $e->getMessage());
            logger()->error('Mail test failed: ' . $e->getMessage());
            return 1;
        }
    }
}

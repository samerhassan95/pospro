<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GrantAllPermissions extends Command
{
    protected $signature = 'permissions:grant-all {email}';
    protected $description = 'Grant all permissions to a user by email';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $this->info("Found user: {$user->name} ({$user->email})");
        
        $allPermissions = Permission::all();
        $user->givePermissionTo($allPermissions);
        
        $this->info("✅ Granted {$allPermissions->count()} permissions to {$user->name}");
        
        return 0;
    }
}

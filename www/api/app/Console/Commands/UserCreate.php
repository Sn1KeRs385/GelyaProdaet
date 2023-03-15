<?php

namespace App\Console\Commands;

use App\Enums\IdentifierType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {login} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create empty user with password. Or change password on exists user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!$this->argument('login') || !$this->argument('password')) {
            throw new \Exception('Login and password is required');
        }

        $user = User::query()
            ->whereHas('identifiers', function (Builder $query) {
                $query->where('type', IdentifierType::LOGIN)
                    ->where('value', $this->argument('login'));
            })
            ->first();

        if (!$user) {
            $user = new User();
            $user->save();
            $user->identifiers()
                ->create([
                    'type' => IdentifierType::LOGIN,
                    'value' => $this->argument('login')
                ]);
        }

        $user->password = Hash::make($this->argument('password'));
        $user->save();
    }
}

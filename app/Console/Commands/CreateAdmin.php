<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--email=} {--name=}';

    protected $description = 'Crea o aggiorna l\'utente amministratore (unico accesso all\'app)';

    public function handle(): int
    {
        $email = $this->option('email') ?: text('Email', required: true);
        $name = $this->option('name') ?: text('Nome', default: 'Admin', required: true);
        $password = password('Password (min 10 caratteri)', required: true);
        $confirm = password('Conferma password', required: true);

        $validator = Validator::make(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $confirm],
            ['email' => ['required', 'email'], 'password' => ['required', 'min:10', 'confirmed']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'is_admin' => true],
        );

        $this->info("Amministratore {$user->email} pronto.");

        return self::SUCCESS;
    }
}

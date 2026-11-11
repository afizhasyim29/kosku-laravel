<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeKosAdmin extends Command
{
    protected $signature = 'kosku:make-admin {email : Email akun yang akan diberi akses pengelola}';

    protected $description = 'Memberi akses pengelola Kos-Ku kepada akun yang sudah terdaftar';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Akun tidak ditemukan. Daftarkan akun tersebut terlebih dahulu.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => 'admin'])->save();
        $this->info("Akses pengelola diberikan kepada {$user->email}.");

        return self::SUCCESS;
    }
}

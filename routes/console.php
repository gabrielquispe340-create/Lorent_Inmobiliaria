<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Comando para crear el enlace public/storage solo si no existe
Artisan::command('storage:ensure-link', function () {
    $publicStorage = public_path('storage');
    if (file_exists($publicStorage)) {
        $this->info('public/storage ya existe — no se crea.');
        return;
    }

    $this->call('storage:link');
    $this->info('Enlace public/storage creado.');
})->purpose('Crea el enlace public/storage solo si no existe');

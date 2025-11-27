<?php

namespace Laravel\Laravel;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

class Bootstrap
{
    protected ApplicationBuilder $builder;
    protected string $basePath; // <-- 1. Tambahkan properti untuk menyimpan path

    /**
     * Membuat instance aplikasi baru.
     */
    public function __construct(string $basePath)
    {
        $this->builder = Application::configure(basePath: $basePath);
        $this->basePath = $basePath; // <-- 2. Simpan base path saat construct
    }

    /**
     * Mendaftarkan routing aplikasi.
     */
    public function withRouting(): static
    {
        // PERBAIKAN SEBENARNYA:
        // Menggunakan properti '$this->basePath' yang kita simpan
        $this->builder->withRouting(
            web: $this->basePath . '/routes/web.php',
            commands: $this->basePath . '/routes/console.php',
            health: '/up',
        );

        return $this;
    }

    /**
     * Mendaftarkan middleware aplikasi.
     */
    public function withMiddleware(): static
    {
        $this->builder->withMiddleware(function (Middleware $middleware) {
            
            // Nama middleware 'CheckRole' Anda sudah benar di sini
            $middleware->alias([
                'role' => \App\Http\Middleware\CheckRole::class,
            ]);

            // CSRF Protection - Exclude logout, session files and health check
            $middleware->validateCsrfTokens(except: [
                'up',
                'logout',  // Exclude logout to prevent 419 error on CSRF token expire
            ]);

        });

        return $this;
    }

    /**
     * Mendaftarkan penanganan pengecualian (exception) aplikasi.
     */
    public function withExceptions(): static
    {
        $this->builder->withExceptions(function (Exceptions $exceptions) {
            //
        });

        return $this;
    }

    /**
     * Mendapatkan instance aplikasi.
     */
    public function get(): Application
    {
        return $this->builder->create();
    }
}
<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Hal pertama yang akan kita lakukan adalah membuat instance aplikasi Laravel
| baru yang berfungsi sebagai "perekat" untuk semua komponen
| Laravel, dan merupakan kontainer IoC untuk semua binding sistem.
|
*/

$app = (new Laravel\Laravel\Bootstrap(dirname(__DIR__)))
    ->withRouting()
    ->withMiddleware()
    ->withExceptions()
    ->get();

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| Skrip ini mengembalikan instance aplikasi. Instance diberikan
| ke skrip pemanggil sehingga kita dapat memisahkan proses pembuatan
| aplikasi dari menjalankannya.
|
*/

return $app;
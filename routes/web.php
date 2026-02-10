<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});



Route::middleware(['auth'])->group(function(){

    Volt::route('products/index', 'products.index')->name('products.index');
    Volt::route('products/create', 'products.create')->name('products.create')->middleware(['role:General Admin|Manager']);
    Volt::route('products/{id}/edit', 'products.edit')->name('products.edit')->middleware(['role:General Admin|Manager']);

    Volt::route('musicplayer', 'musicplayer')->name('musicplayer');
    Volt::route('tracks/create', 'tracks.create')->name('tracks.create');



});

Route::middleware(['auth', 'role:General Admin|Manager'])->group(function(){

    Volt::route('users/index', 'users.index')->name('users.index');
    Volt::route('users/create', 'users.create')->name('users.create');
    Volt::route('users/{id}/edit', 'users.edit')->name('users.edit');

});

Route::middleware(['auth', 'role:General Admin'])->group(function(){

    Volt::route('roles/index', 'roles.index')->name('roles.index');
    Volt::route('roles/create', 'roles.create')->name('roles.create');
    Volt::route('roles/{id}/edit', 'roles.edit')->name('roles.edit');

});

require __DIR__.'/auth.php';

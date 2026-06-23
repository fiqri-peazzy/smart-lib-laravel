<?php

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles
    Role::create(['name' => 'mahasiswa']);
    Role::create(['name' => 'dosen']);
    Role::create(['name' => 'umum']);

    // Seed settings
    (new \Database\Seeders\SystemSettingSeeder)->run();
});

test('mahasiswa gets flat loan limits', function () {
    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('mahasiswa');
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(5); // loan_limit_mahasiswa

    $user->update(['credit_score' => 40]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(5); // still loan_limit_mahasiswa
});

test('dosen gets flat loan limits', function () {
    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('dosen');
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(10); // loan_limit_dosen

    $user->update(['credit_score' => 40]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(10); // still loan_limit_dosen
});

test('umum gets the same limit as mahasiswa', function () {
    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('umum');
    $user->updateMaxLoans();

    expect($user->fresh()->max_loans)->toBe(5);
});

test('credit score is always forced to 100 for everyone', function () {
    $user = User::factory()->create(['credit_score' => 40]);
    $user->recalculateCreditScore();
    expect($user->fresh()->credit_score)->toBe(100.0);
});

test('loan limits can be updated via settings', function () {
    SystemSetting::set('loan_limit_mahasiswa', 10);

    $user = User::factory()->create();
    $user->assignRole('mahasiswa');
    $user->updateMaxLoans();

    expect($user->fresh()->max_loans)->toBe(10);
});

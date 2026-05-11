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

    // Seed settings
    (new \Database\Seeders\SystemSettingSeeder)->run();
});

test('mahasiswa gets correct loan limits based on credit score', function () {
    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('mahasiswa');
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(5); // loan_limit_mahasiswa_90

    $user->update(['credit_score' => 80]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(4); // loan_limit_mahasiswa_70

    $user->update(['credit_score' => 60]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(3); // loan_limit_mahasiswa_50

    $user->update(['credit_score' => 40]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(2); // loan_limit_mahasiswa_default
});

test('dosen gets flat loan limits regardless of credit score', function () {
    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('dosen');
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(25); // loan_limit_dosen

    $user->update(['credit_score' => 40]);
    $user->updateMaxLoans();
    expect($user->fresh()->max_loans)->toBe(25); // still loan_limit_dosen
});

test('dosen credit score is always forced to 100', function () {
    $user = User::factory()->create(['credit_score' => 40]);
    $user->assignRole('dosen');
    $user->recalculateCreditScore();
    expect($user->fresh()->credit_score)->toBe(100.0);
});

test('loan limits can be updated via settings', function () {
    SystemSetting::set('loan_limit_dosen', 50);

    $user = User::factory()->create(['credit_score' => 100]);
    $user->assignRole('dosen');
    $user->updateMaxLoans();

    expect($user->fresh()->max_loans)->toBe(50);
});

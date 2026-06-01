<?php

use App\Filament\Pages\ManageLoanLimits;

it('hides the manage loan limits page from filament navigation', function () {
    $reflection = new ReflectionClass(ManageLoanLimits::class);
    $property = $reflection->getProperty('shouldRegisterNavigation');
    $property->setAccessible(true);

    expect($property->getValue())->toBeFalse();
});

<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('command:initialsetup');

    $user = User::factory()->create();
    Sanctum::actingAs(
        $user,
    );
});

test('example', function () {
    expect(true)->toBeTrue();
});

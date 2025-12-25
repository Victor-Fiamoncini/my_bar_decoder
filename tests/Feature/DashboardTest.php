<?php

use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;
use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Livewire\Dashboard;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertStatus(200);
});

test('user can submit a valid PDF file', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
    $paymentCode = '12345678901234567890123456789012345678901234';

    $this->mock(ExtractPaymentCodeService::class, function ($mock) use ($paymentCode) {
        $mock->shouldReceive('execute')->once()->andReturn($paymentCode);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertSet('paymentCode', $paymentCode)
        ->assertHasNoErrors();
});

test('file input is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('submit')
        ->assertHasErrors(['file' => 'required']);
});

test('only PDF files are accepted', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertHasErrors(['file' => 'mimes']);
});

test('file size cannot exceed 5MB', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 6000, 'application/pdf');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertHasErrors(['file' => 'max']);
});

test('extraction exception displays error message', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeService::class, function ($mock) {
        $mock->shouldReceive('execute')
            ->once()
            ->andThrow(new ExtractPaymentCodeException('Failed to extract payment code'));
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertHasErrors(['file'])
        ->assertSet('paymentCode', '');
});

test('generic exception displays fallback error message', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeService::class, function ($mock) {
        $mock->shouldReceive('execute')
            ->once()
            ->andThrow(new \Exception);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertHasErrors(['file'])
        ->assertSet('paymentCode', '');
});

test('file input is reset after submission', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeService::class, function ($mock) {
        $mock->shouldReceive('execute')->once()->andReturn('12345678901234567890123456789012345678901234');
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('file', $file)
        ->call('submit')
        ->assertSet('file', null);
});

test('user can see their previous documents', function () {
    $user = User::factory()->create();

    Document::factory()->count(3)->create(['name' => 'file.pdf', 'code' => '111', 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertCount('documents', 3);
});

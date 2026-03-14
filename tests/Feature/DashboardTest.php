<?php

use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\Exceptions\FailedToExtractPaymentCodeException;
use App\Core\Modules\Documents\Domain\Entities\Document\Document;
use App\Core\Modules\Documents\Domain\Entities\PaymentCode\Exceptions\ExtractCodeException;
use App\Core\Modules\Documents\Domain\Entities\PaymentCode\PaymentCode;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\ExtractPaymentCodeUseCase;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output\Output;
use App\Livewire\Dashboard;
use App\Models\Document as EloquentDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/dashboard')->assertStatus(200);
});

test('user can submit a single valid PDF file', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
    $codeValue = '12345678901234567890123456789012345678901234567';

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) use ($codeValue) {
        $paymentCode = PaymentCode::tryCreateFromText($codeValue);
        $output = new Output(new Document('document.pdf', $paymentCode, new \DateTimeImmutable, 1));

        $mock->shouldReceive('execute')->once()->andReturn($output);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [$file])
        ->call('submit')
        ->assertSet('extractedPaymentCodes', [['name' => 'document.pdf', 'code' => $codeValue]])
        ->assertHasNoErrors();
});

test('user can submit multiple valid PDF files', function () {
    $user = User::factory()->create();
    $files = [
        UploadedFile::fake()->create('document1.pdf', 100, 'application/pdf'),
        UploadedFile::fake()->create('document2.pdf', 100, 'application/pdf'),
    ];

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $codeValue = '12345678901234567890123456789012345678901234567';
        $paymentCode = PaymentCode::tryCreateFromText($codeValue);
        $output = new Output(new Document('document.pdf', $paymentCode, new \DateTimeImmutable, 1));

        $mock->shouldReceive('execute')->twice()->andReturn($output);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', $files)
        ->call('submit')
        ->assertCount('extractedPaymentCodes', 2)
        ->assertHasNoErrors();
});

test('file input is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('submit')
        ->assertHasErrors(['files' => 'min']);
});

test('files must be an array', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', 'not-an-array')
        ->call('submit')
        ->assertHasErrors(['files' => 'array']);
});

test('at least one file is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [])
        ->call('submit')
        ->assertHasErrors(['files' => 'min']);
});

test('maximum 5 files allowed', function () {
    $user = User::factory()->create();
    $files = collect(range(1, 6))->map(fn ($i) => UploadedFile::fake()->create("document{$i}.pdf", 100, 'application/pdf')
    )->toArray();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', $files)
        ->call('submit')
        ->assertHasErrors(['files' => 'max']);
});

test('only PDF files are accepted', function () {
    $user = User::factory()->create();
    $files = [UploadedFile::fake()->create('document.txt', 100, 'text/plain')];

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', $files)
        ->call('submit')
        ->assertHasErrors(['files.0' => 'mimes']);
});

test('file size cannot exceed 5MB', function () {
    $user = User::factory()->create();
    $files = [UploadedFile::fake()->create('document.pdf', 6000, 'application/pdf')];

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', $files)
        ->call('submit')
        ->assertHasErrors(['files.0' => 'max']);
});

test('extraction exception adds error to errors array', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $mock->shouldReceive('execute')->once()->andThrow(new ExtractCodeException);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [$file])
        ->call('submit')
        ->assertCount('errors', 1)
        ->assertCount('extractedPaymentCodes', 0);
});

test('FailedToExtractPaymentCodeException adds specific error message to errors array', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $mock->shouldReceive('execute')->once()->andThrow(new FailedToExtractPaymentCodeException);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [$file])
        ->call('submit')
        ->assertCount('errors', 1)
        ->assertCount('extractedPaymentCodes', 0)
        ->assertSet('errors', [['name' => 'document.pdf', 'message' => 'Failed to extract payment code from document.pdf']]);
});

test('generic exception adds error to errors array', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $mock->shouldReceive('execute')->once()->andThrow(new \Exception);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [$file])
        ->call('submit')
        ->assertCount('errors', 1)
        ->assertCount('extractedPaymentCodes', 0);
});

test('partial success shows both results and errors', function () {
    $user = User::factory()->create();
    $files = [
        UploadedFile::fake()->create('success.pdf', 100, 'application/pdf'),
        UploadedFile::fake()->create('failure.pdf', 100, 'application/pdf'),
    ];

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $codeValue = '12345678901234567890123456789012345678901234567';
        $paymentCode = PaymentCode::tryCreateFromText($codeValue);
        $output = new Output(new Document('success.pdf', $paymentCode, new \DateTimeImmutable, 1));

        $mock->shouldReceive('execute')
            ->once()
            ->andReturn($output);

        $mock->shouldReceive('execute')
            ->once()
            ->andThrow(new ExtractCodeException);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', $files)
        ->call('submit')
        ->assertCount('extractedPaymentCodes', 1)
        ->assertCount('errors', 1);
});

test('file input is reset after submission', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->mock(ExtractPaymentCodeUseCase::class, function ($mock) {
        $codeValue = '12345678901234567890123456789012345678901234567';
        $paymentCode = PaymentCode::tryCreateFromText($codeValue);
        $output = new Output(new Document('document.pdf', $paymentCode, new \DateTimeImmutable, 1));

        $mock->shouldReceive('execute')->once()->andReturn($output);
    });

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('files', [$file])
        ->call('submit')
        ->assertSet('files', []);
});

test('user can see their previous documents', function () {
    $user = User::factory()->create();

    EloquentDocument::factory()->count(3)->create(['name' => 'file.pdf', 'code' => '111', 'user_id' => $user->id]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    expect($component->recentlyDocuments)->toHaveCount(3);
});

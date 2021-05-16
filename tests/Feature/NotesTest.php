<?php

use Crater\Models\Note;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('retrieve notes', function () {
    getJson('/api/v1/notes')->assertStatus(200);
});

test('create note', function () {
    $note = Note::factory()->raw();

    postJson('/api/v1/notes', $note)->assertStatus(200);

    $this->assertDatabaseHas('notes', $note);
});

test('retrieve note', function () {
    $note = Note::factory()->create();

    getJson("/api/v1/notes/{$note->id}")
        ->assertStatus(200)
        ->assertJson([
            'note' => $note->toArray(),
        ]);
});

test('update note', function () {
    $note = Note::factory()->create();

    $data = Note::factory()->raw();

    putJson("/api/v1/notes/{$note->id}", $data)
        ->assertStatus(200);

    $this->assertDatabaseHas('notes', $data);
});

test('delete note', function () {
    $note = Note::factory()->create();

    deleteJson("/api/v1/notes/{$note->id}")
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDeleted($note);
});

<?php

use Crater\Http\Requests\NotesRequest;
use Illuminate\Validation\Rule;

test('note request validation rules', function () {
    $request = new NotesRequest();

    $this->assertEquals(
        [
            'type' => [
                'required'
            ],
            'name' => [
                'required',
                Rule::unique('notes')
                    ->where('company_id', $request->header('company'))
                    ->where('type', $request->type)
            ],
            'notes' => [
                'required'
            ],
        ],
        $request->rules()
    );
});

test('note request authorize', function () {
    $request = new NotesRequest();

    $this->assertTrue($request->authorize());
});

<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{cid}', function ($user, $cid) {
    return true; //(int) $user->conversation_id === (int) $cid
});

Broadcast::channel('user.{uid}', function () {
    return true; //(int) $user->conversation_id === (int) $cid
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return ['id' => $user->id, 'name' => $user->name];
});

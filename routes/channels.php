<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Programmation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('programmation.{programmationId}.comment', function ($user, $programmationId) {
    return Programmation::where('id', $programmationId)->exists();
});

Broadcast::channel('programmation.{programmationId}.note', function ($user, $programmationId) {
    return Programmation::where('id', $programmationId)->exists();
});

Broadcast::channel('programmation.{programmationId}.link', function ($user, $programmationId) {
    return Programmation::where('id', $programmationId)->exists();
});
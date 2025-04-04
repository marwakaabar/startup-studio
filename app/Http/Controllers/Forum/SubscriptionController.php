<?php

namespace App\Http\Controllers\Forum;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function toggleSubscription(Topic $topic)
    {
        $user = auth()->user();

        $existingSubscription = $topic->subscriptions()->where('user_id', $user->id)->first();

        if ($existingSubscription) {
            $existingSubscription->delete();
            return response()->json(['message' => 'Désabonné du topic']);
        } else {
            $topic->subscriptions()->create(['user_id' => $user->id]);
            return response()->json(['message' => 'Abonné au topic']);
        }
    }
}

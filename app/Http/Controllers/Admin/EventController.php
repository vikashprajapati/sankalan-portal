<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Event;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();

        return view('admin.events.index', compact('events'));
    }

    public function goLive(Event $event)
    {
        if(!$event->setLive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Event is live now!'
        ]);

    }
}

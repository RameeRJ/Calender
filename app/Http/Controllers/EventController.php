<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
       // Fetch all events
       public function index()
       {
           return response()->json(Event::all());
       }
   
       // Store a new event
       public function store(Request $request)
       {
           $request->validate([
               'title' => 'required|string',
               'start' => 'required|date',
               'end' => 'nullable|date',
           ]);
   
           $event = Event::create($request->all());
   
           return response()->json($event, 201); // Return created event with 201 status
       }
   
       // Update an existing event
       public function update(Request $request, $id)
       {
           $event = Event::findOrFail($id);
           $event->update($request->all());
   
           return response()->json($event);
       }
   
       // Delete an event
       public function destroy($id)
       {
           $event = Event::findOrFail($id);
           $event->delete();
   
           return response()->json(['message' => 'Event deleted successfully']);
       }
}
<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Mail\ScheduleCreated;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if($request->keyword){
            
            $user = auth()->user();
            $schedules = $user->schedules()
            ->where('title', 'LIKE', '%'.$request->keyword.'%')
            ->orWhere('description', 'LIKE', '%'.$request->keyword.'%')
            ->paginate(2);
        }else{
            
            $user = auth()->user();
            $schedules = $user->schedules()->paginate(2);
        }
        
        return view('schedules.index', compact('schedules'));
        
    }
    
    public function create()
    {
        return view('schedules.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:10',
        ],[
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'title.min' => 'Title need atleast 5 characters',
            'description.min' => 'Description need atleast 10 characters'
        ]);
        
        $schedule = new Schedule();
        $schedule->title = $request->title;
        $schedule->description = $request->description;
        $schedule->user_id = auth()->user()->id;
        $schedule->save();
        
        if($request->hasFile('attachment')){
            $filename = $schedule->id.'-'.date("Y-m-d").'.'.$request->attachment->getClientOriginalExtension();
            Storage::disk('public')->put($filename, File::get($request->attachment));
            
            $schedule->attachment = $filename;
            $schedule->save();
        }

        Mail::to('your@email.com')->send(new ScheduleCreated($schedule));
        dispatch(new SendEmailJob($schedule));

        return redirect()
        ->route('schedule:index')
        ->with([
            'alert-type' => 'alert-primary',
            'alert' => 'Your schedule has been saved!'
        ]);
    }
    
    public function show(Schedule $schedule)
    {
        $this->authorize('show', $schedule);
        
        return view('schedules.show', compact('schedule'));
    }
    
    public function edit(Schedule $schedule)
    {
        $this->authorize('edit', $schedule);
        
        return view('schedules.edit', compact('schedule'));
    }
    
    public function update(Schedule $schedule, Request $request)
    {
        $schedule->title = $request->title;
        $schedule->description = $request->description;
        $schedule->save();
        
        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-success',
            'alert' => 'Your schedule has been updated!'
        ]);
    }
    
    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);
        
        if($schedule->attachment){
            Storage::disk('public')->delete($schedule->attachment);
        }
        
        $schedule->delete();
        
        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-danger',
            'alert' => 'Your schedule has been deleted!'
        ]);
    }
    
    public function forceDestroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);
        
        $schedule->forceDelete();
        
        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-danger',
            'alert' => 'Your schedule has been force deleted!'
        ]);
    }
}
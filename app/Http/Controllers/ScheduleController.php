<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use File;
use Storage;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if($request->keyword){
            //search by title
            $user = auth()->user();
            $schedules = $user->schedules()
                ->where('title', 'LIKE', '%'.$request->keyword.'%')
                ->orWhere('description', 'LIKE', '%'.$request->keyword.'%')
                ->paginate(2);
        }else{
            // query all schedule from 'schedules' table to $schedules
            // select * from schedules - SQL Query
            //$schedules = Schedule::all();
            $user = auth()->user();
            $schedules = $user->schedules()->paginate(2);
        }

        // return to view with $schedules
        // resurces/views/schedules/index.blade.php
        return view('schedules.index', compact('schedules'));

    }

    public function create()
    {
        // this is schedule create form
        // show create form
        // resources/views/schedules/create.blade.php
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:100',
        ],[
            'title.required' => 'Wajib isi tajuk',
            'description.required' => 'Wajib isi description',
            'title.min' => 'Sila isi melebihi 5'
        ]);

        // store all input to table 'schedules' using model Schedule
        $schedule = new Schedule();
        $schedule->title = $request->title;
        $schedule->description = $request->description;
        $schedule->user_id = auth()->user()->id;
        $schedule->save();

        if($request->hasFile('attachment')){
            // rename file - 5-2021-09-03.jpg/xls
            $filename = $schedule->id.'-'.date("Y-m-d").'.'.$request->attachment->getClientOriginalExtension();

            // store attachment on storage
            Storage::disk('public')->put($filename, File::get($request->attachment));

            // update row
            $schedule->attachment = $filename;
            $schedule->save();
        }

        // return to index
        return redirect()
            ->route('schedule:index')
            ->with([
                'alert-type' => 'alert-primary',
                'alert' => 'Your schedule has been saved!'
            ]);
    }

    public function show(Schedule $schedule)
    {
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        return view('schedules.edit', compact('schedule'));
    }

    public function update(Schedule $schedule, Request $request)
    {
        // update $schedule using input from edit form
        $schedule->title = $request->title;
        $schedule->description = $request->description;
        $schedule->save();

        // redirect to schedule index
        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-success',
            'alert' => 'Your schedule has been updated!'
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        if($schedule->attachment){
            Storage::disk('public')->delete($schedule->attachment);
        }

        // delete $schedule from  db
        $schedule->delete();

        // return to schedule index
        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-danger',
            'alert' => 'Your schedule has been deleted!'
        ]);
    }

    public function forceDestroy(Schedule $schedule)
    {
        $schedule->forceDelete();

        return redirect()->route('schedule:index')->with([
            'alert-type' => 'alert-danger',
            'alert' => 'Your schedule has been force deleted!'
        ]);
    }
}
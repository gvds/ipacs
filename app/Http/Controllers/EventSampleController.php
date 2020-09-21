<?php

namespace App\Http\Controllers;

use App\event_sample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class EventSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|regex:/^[A-Z]{0,6}\d{3,8}$/|exists:event_sample,barcode'
        ]);
        if (array_key_exists('barcode', $validatedData)) {
            $sample = event_sample::join('sampletypes', 'sampletype_id', '=', 'sampletypes.id')
                ->select('event_sample.*')
                ->where('barcode', $validatedData['barcode'])
                ->where('project_id', session('currentProject'))
                ->first();
            if (!is_null($sample)) {
                $subject = $sample->event_subject->subject;
                if ($subject->site_id !== auth()->user()->project_site) {
                    return back()->withErrors('This sample does not belong to your site');
                }
                return redirect("/samples/$sample->id");
            } else {
                return view('samples.index')->withErrors("Sample " . $validatedData['barcode'] . " was not found in this project");
            }
        }
        return view('samples.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function show(event_sample $event_sample)
    {
        if ($event_sample->samplestatus_id == 0) {
            return back()->withErrors("Sample barcode " . $event_sample->barcode . " is currently unassigned");
        }
        $subject = $event_sample->event_subject->subject;
        if ($subject->project_id !== session('currentProject')) {
            return back()->withErrors('This sample does not belong to the current project');
        }
        if ($subject->site_id !== auth()->user()->project_site) {
            return back()->withErrors('This sample does not belong to your site');
        }
        return view('samples.show', compact('event_sample'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function edit(event_sample $event_sample)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, event_sample $event_sample)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(event_sample $event_sample)
    {
        //
    }

    /**
     * Unlog the specified sample.
     *
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function unlog(event_sample $event_sample)
    {
        $event_sample->samplestatus_id = 0;
        $event_sample->save();
        return redirect('/samples')->with('message', "Sample $event_sample->barcode has been unlogged");
    }

    /**
     * Update the specified sample's volume.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\event_sample  $event_sample
     * @return \Illuminate\Http\Response
     */
    public function volumeUpdate(Request $request, event_sample $event_sample)
    {
        $validatedData = $request->validate([
            'volume' => 'required|numeric'
        ]);
        $event_sample->volume = $validatedData['volume'];
        $event_sample->save();
        return back()->with('message', "Volume has been updated");
    }

    public function search()
    {
        $events = \App\project::select(DB::raw("CONCAT(arms.name,' : ',events.name) AS event, events.id"))
            ->join('arms', 'projects.id', 'project_id')
            ->join('events', 'arms.id', 'arm_id')
            ->where('projects.id', session('currentProject'))
            ->orderBy('arm_num')
            ->orderBy('offset')
            ->pluck('event', 'events.id');
        $sampletypes = \App\sampletype::where('project_id', session('currentProject'))
            ->orderBy('name')
            ->pluck('name', 'id');
        $sites = \App\site::where('project_id', session('currentProject'))
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('samples.search', compact('events', 'sampletypes', 'sites'));
    }

    public function retrieve(Request $request)
    {

        $validatedData = $request->validate([
            'subjectIDlist' => 'nullable|max:6000',
            'events' => 'array',
            'sampletypes' => 'array',
            'sites' => 'array',
            'events.*' => 'nullable|integer',
            'sampletypes.*' => 'nullable|integer',
            'sites.*' => 'nullable|integer',
        ]);
        $subjectIDs = array_unique(array_filter(preg_split("(,|\s+)", $validatedData['subjectIDlist'])));
        $subjectIDs = empty($subjectIDs) ? null : $subjectIDs;
        $events = empty($validatedData['events']) ? null : $validatedData['events'];
        $sampletypes = empty($validatedData['sampletypes']) ? null : $validatedData['sampletypes'];
        $sites = empty($validatedData['sites']) ? null : $validatedData['sites'];

        $samples = event_sample::with('event_subject.event','event_subject.subject', 'event_subject.event.arm','site','sampletype','status','location')
        // ->select('barcode','aliquot','volume')
        ->whereHas('sampletype', function ($query) {
            return $query->where('project_id', session('currentProject'));
        })
            ->when($subjectIDs, function ($query, $subjectIDs) {
                return $query->whereHas('event_subject', function ($query) use ($subjectIDs) {
                    return $query->whereIn('subject_id', $subjectIDs);
                });
            })
            ->when($sampletypes, function ($query, $sampletypes) {
                return $query->whereIn('sampletype_id', $sampletypes);
            })
            ->when($events, function ($query, $events) {
                return $query->whereHas('event_subject', function ($query) use ($events) {
                    return $query->whereIn('event_id', $events);
                });
            })
            ->when($sites, function ($query, $sites) {
                return $query->whereHas('event_subject', function ($query) use ($sites) {
                    return $query->whereHas('subject', function ($query) use ($sites) {
                        return $query->whereIn('site_id', $sites);
                    });
                });
            })
            ->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="samplelist.csv"',
        ];
        
        $data = "Barcode\tSampleType\tArm\tEvent\tAlquot\tVolume\tStatus\tSubjectID\tSite\tParent\tLocation\n";
        foreach ($samples as $key=>$sample) {
            $sampledata = [$sample->barcode,
            $sample->sampletype->name,
            $sample->event_subject->event->arm->name,
            $sample->event_subject->event->name,
            $sample->aliquot,
            $sample->volume . $sample->sampletype->volumeUnit,
            $sample->status->samplestatus,
            $sample->event_subject->subject->subjectID,
            $sample->site->name,
            $sample->parentBarcode];
            if (!empty($sample->location)) {
                array_push($data, $sample->location->virtualUnit_id . ' - ' . $sample->location->rack . ':' . $sample->location->box . ':' . $sample->location->position);
            }

            $data .= implode("\t",$sampledata) . "\n";
        }
        return Response::make($data, 200, $headers);
        
    }
}

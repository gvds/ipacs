<?php

namespace App\Http\Controllers;

use App\event_subject;
use Illuminate\Http\Request;
use App\Library\PDF_Label;
use Carbon\Carbon;

class LabelController extends PDF_Label
{
    private $fpdf;

    public function __construct()
    {
        define('FPDF_FONTPATH', public_path() . '/font');
    }

    public function printLabels()
    {
        $events = event_subject::where('labelStatus', '1')
            ->join('subjects', 'subject_id', 'subjects.id')
            ->join('events', 'event_id', 'events.id')
            ->join('arms', 'events.arm_id', 'arms.id')
            ->select([
                'subjects.project_id',
                'arms.name AS armname',
                'events.name AS eventname',
                'events.id AS event_id',
                'subjectID',
                'firstname',
                'surname',
                'subject_event_labels',
                'name_labels',
                'study_id_labels'
            ])
            ->where('subjects.project_id', session('currentProject'))
            ->where('user_id', auth()->user()->id)
            ->where('active', true)
            ->get();

        /*------------------------------------------------
            To create the object, 2 possibilities:
            either pass a custom format via an array
            or use a built-in AVERY name
            ------------------------------------------------*/

        // Example of custom format
        // $pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99, 'height'=>38, 'font-size'=>14));

        // Standard format
        $this->fpdf = new PDF_Label('L7651_mod');

        $this->fpdf->AddPage();
        $this->fpdf->AddFont('Calibri', '', 'calibri.php');
        $this->fpdf->SetFont('Calibri', '', 8);
        // $this->fpdf->AddFont('EBGaramond', '', 'EBGaramond-VariableFont_wght.php');
        // $this->fpdf->SetFont('EBGaramond', '', 8);

        foreach ($events as $event) {
            // Generate Name labels
            $PSE = $event->project_id . '_' . $event->subjectID . '_' . $event->event_id;
            for ($i = 0; $i < $event->name_labels; $i++) {
                $text = sprintf("%s %s\n%s\nEvent: %s\nArm: %s", $event->firstname, $event->surname, $PSE, $event->eventname, $event->armname);
                $this->fpdf->Add_BarLabel($text, $PSE);
            }
            // Generate Study ID labels
            for ($i = 0; $i < $event->study_id_labels; $i++) {
                $text = sprintf("%s", $event->subjectID);
                $this->fpdf->Add_BarLabel($text, $event->subjectID);
            }
            // Generate PSE labels
            for ($i = 0; $i < $event->subject_event_labels; $i++) {
                $text = sprintf("%s\nEvent: %s\nArm: %s", $PSE, $event->eventname, $event->armname);
                $this->fpdf->Add_BarLabel($text, $PSE);
            }
        }

        $this->fpdf->Output("labels.pdf", "D");
    }

    public function labelqueue()
    {
        $events = event_subject::where('labelStatus', '1')
            ->join('subjects', 'subject_id', 'subjects.id')
            ->join('events', 'event_id', 'events.id')
            ->join('arms', 'events.arm_id', 'arms.id')
            ->select([
                'event_subject.id',
                'subjects.project_id',
                'arms.name AS armname',
                'events.name AS eventname',
                'events.id AS event_id',
                'arms.id AS arm_id',
                'subjectID',
                'eventDate'
            ])
            ->where('subjects.project_id', session('currentProject'))
            ->where('user_id', auth()->user()->id)
            ->where('active', true)
            ->get();
        return view('eventlabels.index', compact('events'));
    }

    public function addEventsToLabelQueue()
    {
        \App\Label::addEventsToLabelQueue();
    }
    // public function addEventsToLabelQueue($thresholdDate = null)
    // {
    //     $thresholdDate = Carbon::parse('next friday');
    //     $records = event_subject::where('labelStatus', '0')
    //         ->join('events', 'event_id', 'events.id')
    //         ->join('arms', 'arm_id', 'arms.id')
    //         ->where('project_id', session('currentProject'))
    //         ->where('minDate', "<=", $thresholdDate)
    //         ->where('active', true)
    //         ->update(['labelStatus' => 1]);
    //     return redirect('/')->with('message', "$records events added to the label queue");
    // }

    public function clear(Request $request)
    {
        $request->validate([
            'label_id.*' => 'integer|exists:event_subject,id'
        ]);
        if (is_array($request->label_ids)) {
            $affected = event_subject::whereIn('id', $request->label_ids)
                ->update(['labelStatus' => 2]);
        } else {
            $affected = 0;
        }
        return redirect('/labels')->with('message', $affected . " labels cleared from the queue");
    }

    public function addEventToLabelQueue(event_subject $event_subject)
    {
        $subject_id = $event_subject->subject_id;
        $event_subject->labelStatus = 1;
        $event_subject->save();
        return redirect("/subjects/$subject_id");
    }
}
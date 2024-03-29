<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;

class ScheduleController extends Controller
{
    private $fpdf;

    public function __construct()
    {
        define('FPDF_FONTPATH', public_path() . '/font');
    }

    public function generate(Request $request, $week)
    {
        try {

            $currentProject = request('currentProject');

            if (!in_array($week, ['thisweek', 'nextweek'])) {
                return redirect()->back()->with('error', 'Invalid schedule week specified');
            }

            if ($week == 'nextweek') { // Next week's schedule
                $startdate = Carbon::parse('next monday');
            } else { // This week's schedule
                $startdate = Carbon::parse('monday this week');
            }

            $hstartdate = $startdate->format("d/m/Y"); // formatted for header
            $enddate = $startdate->add(6, 'days');
            $henddate = $enddate->format('d/m/Y'); // formatted for header
            $header = "($hstartdate - $henddate)";
            // Get the fields to use in the schedule
            // $query = "Select plims.lu_schedule_fields.fieldname, plims.lu_schedule_fields.fieldtitle, plims.lu_schedule_fields.fieldlength, plims.lu_schedule_fields.fieldorder From plims.lu_schedule_fields Where plims.lu_schedule_fields.project_id = $PM_project_id Order By plims.lu_schedule_fields.fieldorder";
            // $result = $conn->query($query);
            $field_arr = array();

            $this->fpdf = new Fpdf('L');
            $this->fpdf->AddFont('Calibri', 'B', 'calibrib.php');
            $this->fpdf->SetDisplayMode('fullpage');
            $this->fpdf->SetMargins(5, 5);
            $this->fpdf->AddPage();
            $this->fpdf->SetFont('Calibri', 'B', 16);
            $this->fpdf->Cell(0, 9, "$currentProject->project project Followup Schedule - $header", 0, 1, 'C');
            $this->fpdf->SetFont('Calibri', 'B', 11);
            $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');
            $this->fpdf->Cell(26, 7, "Subject", '', 0, 'C');
            $this->fpdf->Cell(50, 7, "Name", '', 0, 'C');
            $this->fpdf->Cell(35, 7, "Event", '', 0, 'C');
            $this->fpdf->Cell(25, 7, "Due Date", '', 0, 'C');
            $this->fpdf->Cell(25, 7, "Start Date", '', 0, 'C');
            $this->fpdf->Cell(25, 7, "End Date", '', 0, 'C');
            $this->fpdf->Cell(25, 7, "Address", '', 0, 'C');
            // $fieldtitle = "";
            // for ($index = 0; $index < count($field_arr); $index++) {
            //     if ($field_arr[$index]['fieldtitle'] != $fieldtitle) {
            //         $fieldtitle = $field_arr[$index]['fieldtitle'];
            //         $this->fpdf->Cell($field_arr[$index]['fieldlength'], 7, $field_arr[$index]['fieldtitle'], '', 0, 'L');
            //     }
            // }

            $this->fpdf->Cell(0, 7, "", '', 1, 'L');
            $this->fpdf->Cell(0, 0, '', 'T', 1, 'L');

            $this->fpdf->SetFillColor(220, 220, 220);

            // Get ids of current user and users for whom this user is substituting
            $currentSubstitutees = auth()->user()->substitutees;
            $userIDList = [auth()->user()->id];
            foreach ($currentSubstitutees as $substitutee) {
                array_push($userIDList, $substitutee->id);
            }

            // Schedule events
            \App\event_subject::whereHas('subject', function ($query) use ($userIDList) {
                $query->where('project_id', session('currentProject'))
                    ->whereIn('user_id', $userIDList)
                    ->where('subject_status', 1);
            })
                ->whereHas('event', function ($query) {
                    $query->where('active', true);
                })
                ->where('minDate', '<=', $enddate)
                ->where('eventstatus_id', '<', 2)
                ->update(['eventstatus_id' => 2]);

            $subjects = \App\subject::with(['events' => function ($query) {
                $query->where('eventstatus_id', 2)
                    ->where('active', true)
                    ->orderBy('eventDate');
            }])
                ->where('project_id', $currentProject->id)
                ->where('user_id', auth()->user()->id)
                ->where('subject_status', 1)
                ->orderBy('subjectID')
                ->get();
            $fill = 1;
            foreach ($subjects as $subject) {
                foreach ($subject->events as $event) {
                    $address = implode(', ', array_filter([$subject->address1, $subject->address2, $subject->address3]));
                    $fill = $fill ? 0 : 1;
                    $this->fpdf->SetFont('Arial', '', 10);
                    $this->fpdf->Cell(26, 9, $subject->subjectID, 0, 0, 'C', $fill);
                    $this->fpdf->Cell(50, 9, $subject->fullname, 0, 0, 'C', $fill);
                    $this->fpdf->Cell(35, 9, $event->name, 0, 0, 'C', $fill);
                    $this->fpdf->SetFont('Arial', 'B', 10);
                    $this->fpdf->Cell(25, 9, $event->pivot->eventDate, 0, 0, 'C', $fill);
                    $this->fpdf->SetFont('Arial', '', 10);
                    $this->fpdf->Cell(25, 9, $event->pivot->minDate, 0, 0, 'C', $fill);
                    $this->fpdf->Cell(25, 9, $event->pivot->maxDate, 0, 0, 'C', $fill);
                    $this->fpdf->SetFont('Arial', '', 9);
                    $this->fpdf->Cell(0, 9, $address, 0, 1, 'L', $fill);
                }
            }

            $this->fpdf->Output("schedule.pdf", "I");
        } catch (\Throwable $th) {
            return redirect('/')->with('error', $th->getMessage());
        }
    }
}

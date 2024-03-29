<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
    <x-slot name='secondary'>

      @include('subjects._search')

    </x-slot>
  </x-pageheader>

  @include('layouts.message')

  <div class='flex'>
    <div class='flex-col'>
      <div class='text-lg font-bold flex justify-between mb-1'>
        <span>Details</span>
        @if ($subject->subject_status > 0)
        <span>
          <x-buttonlink href="/subjects/{{$subject->id}}/edit">Edit</x-buttonlink>
        </span>
        @endif
      </div>
      <div>
        <x-table class="table table-sm">
          <tr>
            <td class='font-bold'>Name</td>
            <td>{{$subject->fullname}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Current Arm</td>
            <td>{{$subject->arm->name}}</td>
          </tr>
          <tr>
            <td class='font-bold flex'>Address</td>
            <td>{{$subject->address1}}<br />{{$subject->address2}}<br />{{$subject->address3}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Site</td>
            <td>{{$subject->site->name}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Owner</td>
            <td>{{$subject->user->fullname}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Enrol Date</td>
            <td>{{$subject->enrolDate}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Arm Baseline Date</td>
            <td>{{$subject->armBaselineDate}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Previous Arm</td>
            <td>
              @if ($subject->previous_arm)
              {{$subject->previous_arm->name}}
              @endif
            </td>
          </tr>
          <tr>
            <td class='font-bold'>Previous Arm Baseline Date</td>
            <td>{{$subject->previousArmBaselineDate}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Status</td>
            <td>
              @switch($subject->subject_status)
              @case(0)
              Unenrolled
              @break
              @case(1)
              Enrolled
              @break
              @case(2)
              Dropped
              @break
              @default
              Error
              @endswitch
            </td>
          </tr>
        </x-table>
      </div>
      <div class='flex space-x-2' x-data="confirmDrop()">
        @if ($subject->subject_status === 1)
        @permission('administer-project',$currentProject->team->name)
        <x-buttonlink href="/subjects/{{$subject->id}}/changeDate" class="bg-blue-700 text-blue-50">Change Arm Date
        </x-buttonlink>
        @endpermission
        {{ Form::open(['url' => "/subjects/$subject->id/drop", 'method' => 'POST', 'x-on:click.away'=>'clear()']) }}
        <x-buttonlink @click="del()" x-text=" getDeleteText()" class="text-red-50" x-bind:class="getDeleteBg()">Drop
          Subject</x-buttonlink>
        {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-red-600 text-red-50 text-sm font-bold px-2
        py-1
        mt-1
        rounded shadow-md leading-tight hover:text-indigo-500"]) }}
        {{ Form::close() }}
        @elseif ($subject->subject_status === 2)
        {{ Form::open(['url' => "/subjects/$subject->id/restore", 'method' => 'POST', 'x-on:click.away'=>'clear()']) }}
        <x-buttonlink @click="restore()" x-text=" getRestoreText()" class="text-red-50" x-bind:class="getRestoreBg()">Restore Subject
        </x-buttonlink>
        {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-indigo-600 text-red-50 text-sm font-bold px-2
        py-1 rounded shadow-md leading-tight hover:text-white"]) }}
        {{ Form::close() }}
        @endif
      </div>
    </div>

    <div class='ml-20'>
      @if ($subject->subject_status === 1 & count($switcharms) > 0)
      <div class='text-lg font-bold'>Switch Arm</div>
      {{ Form::open(['url' => "/subjects/$subject->id/switch", 'class' => 'form mb-2', 'method' => 'POST']) }}
      {{ Form::select('switchArm', $switcharms, null, ['required','placeholder' => 'Select new arm...']) }}
      {{ Form::date('switchDate', today(), ['required', 'class'=>'mt-2']) }}
      {{ Form::submit('Switch', ['class' => 'w-full mt-2']) }}
      {{ Form::close() }}
      @endif
      @if ($subject->previous_arm)
      <div class='text-lg font-bold'>Reverse Previous Arm Switch</div>
      {{ Form::open(['url' => "/subjects/$subject->id/reverseSwitch", 'class' => 'form', 'method' => 'POST']) }}
      {{ Form::submit('Reverse Switch', ['class' => 'w-full mt-2']) }}
      {{ Form::close() }}
      @endif
    </div>

  </div>

  @if ($subject->subject_status === 0)

  <div class='text-lg font-bold'>Enrol Subject</div>
  {!! Form::open(['url' => "/subjects/$subject->id/enrol", 'class' => 'form', 'method' => 'POST']) !!}
  {{ Form::label('enrolDate','Enrolment Date')}}
  {{ Form::date('enrolDate') }}
  {{ Form::label('firstname','First Name')}}
  {{ Form::text('firstname') }}
  {{ Form::label('surname','Surname')}}
  {{ Form::text('surname') }}
  {{ Form::label('address','Address')}}
  {{ Form::text('address1') }}
  {{ Form::text('address2') }}
  {{ Form::text('address3') }}
  {{ Form::submit('Enrol', ['class' => 'w-full mt-1']) }}
  {{ Form::close() }}

  @endif

  <div class='mt-5'>
    <div class='text-lg font-bold w-auto'>Events</div>
    <x-table>
      <x-slot name='head'>
        <th>Arm</th>
        <th>Event ID</th>
        <th>Event</th>
        <th>Iteration</th>
        <th>Status</th>
        <th>Label Status</th>
        <th>Min Date</th>
        <th>Date</th>
        <th>Max Date</th>
        {{-- <th>Registered</th> --}}
        <th>Logged</th>
      </x-slot>
      @foreach ($events as $key => $event)
      <tr class='odd:bg-gray-100 text-xs'>
        <td>{{$event->arm->name}}</td>
        <td>{{$event->pivot->id}}</td>
        <td>{{$event->name}}</td>
        <td>
          {{$event->pivot->iteration}}
          @if ($event->repeatable & in_array($event->pivot->eventstatus_id, [3,4]))
          @if (!isset($events[$key+1]) or $events[$key+1]->id !== $event->id)
          <span class='text-blue-700' x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
            {{ Form::open(['url' => "/subjects/$subject->id/addEvent",'method' => 'POST','style'=>'display:inline-block']) }}
            {{ Form::hidden('event_subject_id', $event->pivot->id) }}
            {{ Form::hidden('iteration', $event->pivot->iteration) }}
            <button class='bg-gray-300 rounded shadow px-2 py-1 ml-4'>+</button>
            {{ Form::close() }}
            <span x-show="open" class='absolute text-xs bg-gray-200 border border-gray-200 rounded shadow ml-1 px-2'>
              Add Iteration
            </span>
          </span>
          @endif
          @endif
        </td>
        <td>{{$eventstatus[$event->pivot->eventstatus_id]->eventstatus}}</td>
        <td>
          <div class='flex items-center justify-between'>
            @switch($event->pivot->labelStatus)
            @case(1)
            Queued
            @break
            @case(2)
            Printed
            @break
            @default
            Pending
            @endswitch
            @if (($event->pivot->eventstatus_id !== 6 and $event->pivot->labelStatus === 2) or
            (in_array($event->pivot->eventstatus_id,[0,1,2]) and $event->pivot->labelStatus !== 1))
            <span x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
              <a href="/labels/{{$event->pivot->id}}/queue"
                class='bg-blue-200 text-blue-900 font-semibold text-xs py-1 px-2 rounded shadow-md cursor-pointer hover:text-indigo-600'>
                Q
              </a>
              <span x-show="open" class='absolute text-xs bg-blue-700 border border-blue-900 text-white rounded shadow ml-1 px-2'>
                Add to label queue
              </span>
            </span>
            @endif
          </div>
        </td>
        <td>
          {{$event->pivot->minDate}}
        </td>
        <td>
          {{$event->pivot->eventDate}}
        </td>
        <td>
          {{$event->pivot->maxDate}}
        </td>
        <td>
          @if ($event->pivot->logDate)
          {{Carbon\Carbon::parse($event->pivot->logDate)->format('Y-m-d')}}
          @endif
        </td>
        <td>
          {{ Form::open(['url' => '/event_subject/retrieve','method' => 'GET']) }}
          {{ Form::hidden('pse', $subject->project_id . '_' . $subject->subjectID . '_' . $event->pivot->id) }}
          <button class='bg-gray-300 rounded shadow px-2 py-1 m-0'>Log</button>
          {{ Form::close() }}
        </td>
      </tr>
      @endforeach
    </x-table>
  </div>

</x-layout>

<script>
  function confirmDrop() {
    return {
      showConfirm: false,
      deleteText: "Drop Subject",
      deleteBgCol: "bg-red-600",
      restoreText: "Restore Subject",
      restoreBgCol: "bg-indigo-600",
      del() {
        this.showConfirm = !this.showConfirm;
        if (this.showConfirm) {
          this.deleteBgCol = 'bg-green-600';
          this.deleteText = "Cancel";
        } else {
          this.deleteBgCol = 'bg-red-600';
          this.deleteText = "Drop Subject";
        }
      },
      restore() {
        this.showConfirm = !this.showConfirm;
        if (this.showConfirm) {
          this.restoreBgCol = 'bg-green-600';
          this.restoreText = "Cancel";
        } else {
          this.restoreBgCol = 'bg-indigo-600';
          this.restoreText = "Restore Subject";
        }
      },
      clear() {
        this.showConfirm = false;
        this.deleteBgCol = 'bg-red-600';
        this.deleteText = "Drop Subject";
        this.restoreBgCol = 'bg-indigo-600';
        this.restoreText = "Restore Subject";
      },
      confirming() {
        return this.showConfirm === true
      },
      getDeleteText() {
        return this.deleteText
      },
      getDeleteBg() {
        return this.deleteBgCol
      },
      getRestoreText() {
        return this.restoreText
      },
      getRestoreBg() {
        return this.restoreBgCol
      },
    }
  }
</script>

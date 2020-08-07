<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
    <x-slot name='button'>
      <x-buttonlink href='/subjects' class='text-orange-700'>Return</x-buttonlink>
    </x-slot>
  </x-pageheader>

  @include('layouts.errormsg')

  <div class='flex'>
    <div class='flex-col'>
      <div class='text-lg font-bold'>Details</div>
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
      @if ($subject->subject_status === 1)
      <span x-data="confirmDrop()"">
        {{ Form::open(['url' => "/subjects/$subject->id/drop", 'method' => 'POST', 'x-on:click.away'=>'clear()']) }}
        <x-buttonlink @click=" del()" x-text=" getDeleteText()" class="text-red-50" x-bind:class="getDeleteBg()">Drop
        Subject</x-buttonlink>
        {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-red-600 text-red-50 text-sm font-bold px-2 py-1 rounded shadow-md leading-tight hover:text-indigo-500"]) }}
        {{ Form::close() }}
      </span>
      @endif
      @if ($subject->subject_status === 2)
      <span x-data="confirmDrop()"">
        {{ Form::open(['url' => "/subjects/$subject->id/restore", 'method' => 'POST', 'x-on:click.away'=>'clear()']) }}
        <x-buttonlink @click=" restore()" x-text=" getRestoreText()" class="text-red-50"
        x-bind:class="getRestoreBg()">Restore Subject</x-buttonlink>
        {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-indigo-600 text-red-50 text-sm font-bold px-2 py-1 rounded shadow-md leading-tight hover:text-white"]) }}
        {{ Form::close() }}
      </span>
      @endif
    </div>

    <div class='ml-20'>
      @if ($subject->subject_status === 1 & count($switcharms) > 0)
      <div class='text-lg font-bold'>Switch Arm</div>
      {{ Form::open(['url' => "/subjects/$subject->id/switch", 'class' => 'form mb-2', 'method' => 'POST']) }}
      {{ Form::select('switchArm', $switcharms, null, ['required','placeholder' => 'Select new arm...']) }}
      {{ Form::date('switchDate', today(), ['required']) }}
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

  @else

  <div class='mt-5'>
    <div class='text-lg font-bold w-auto'>Events</div>
    <x-table>
      <x-slot name='head'>
        <th>Arm</th>
        <th>Event ID</th>
        <th>Event</th>
        <th>Status</th>
        <th>Min Date</th>
        <th>Date</th>
        <th>Max Date</th>
        <th>Registered</th>
        <th>Logged</th>
      </x-slot>
      @foreach ($events as $event)
      <tr class='odd:bg-gray-100'>
        <td>{{$event->arm->name}}</td>
        <td>{{$event->id}}</td>
        <td>{{$event->name}}</td>
        <td>{{$eventstatus[$event->pivot->eventstatus_id]->eventstatus}}</td>
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
          @if ($event->pivot->reg_timestamp)
          {{Carbon\Carbon::parse($event->pivot->reg_timestamp)->format('Y-m-d H:i')}}
          @endif
        </td>
        <td>
          @if ($event->pivot->log_timestamp)
          {{Carbon\Carbon::parse($event->pivot->log_timestamp)->format('Y-m-d H:i')}}</td>
        @endif
      </tr>
      @endforeach
    </x-table>
  </div>

  @endif
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
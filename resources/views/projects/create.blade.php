<x-layout>

    <x-pageheader>
        New Project
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/project', 'class' => 'form']) }}
    {{ Form::label('project', 'Project Name') }}
    {{ Form::text('project', null, ['required','maxlength'=>'50']) }}
    {{ Form::label('owner', 'Owner', ['class'=>'text-sm']) }}
    {{ Form::select('owner', $users, null, ['required']) }}
    {{ Form::label('subject_id_prefix', 'SubjectID Prefix') }}
    {{ Form::text('subject_id_prefix', null, ['maxlength'=>'6']) }}
    {{ Form::label('subject_id_digits', 'SubjectID Digits') }}
    {{ Form::selectRange('subject_id_digits', 2, 6, 3) }}
    {{ Form::label('storageProjectName', 'Storage Project Name') }}
    {{ Form::text('storageProjectName', null, ['maxlength'=>'15']) }}
    {{ Form::label('label_id', 'Label Format') }}
    {{ Form::select('label_id', ['L7651_mod' => 'L7651_mod', 'L7651' => 'L7651'], null) }}
    {{ Form::submit('Submit', ['class' => 'w-full mt-2']) }}
    <x-buttonlink :href="url('/project')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
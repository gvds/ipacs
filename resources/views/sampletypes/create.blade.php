<x-layout>
    <x-pageheader>
        New Sample Type
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sampletypes', 'class'=>'form']) }}
    {{ Form::label('name', 'Sample Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('primary', 'Primary') }}
    {{ Form::radio('primary', 0, true) }} No {{ Form::radio('primary', 1) }} Yes
    {{ Form::label('aliquots', 'Aliquots') }}
    {{ Form::selectRange('aliquots', 1,20) }}
    {{ Form::label('pooled', 'Pooled') }}
    {{ Form::radio('pooled', 0, true) }} No {{ Form::radio('pooled', 1) }} Yes
    {{ Form::label('defaultVolume', 'Default Volume') }}
    {{ Form::text('defaultVolume', null) }}
    {{ Form::label('volumeUnit', 'Volume Unit') }}
    {{ Form::text('volumeUnit', null) }}
    {{ Form::label('transferDestination', 'Transfer Destination') }}
    {{ Form::select('transferDestination[]', $destinations, null, ['multiple']) }}
    {{ Form::label('sampleGroup', 'Sample Group') }}
    {{ Form::text('sampleGroup', null) }}
    {{ Form::label('tubeLabelType_id', 'Tube Label Type') }}
    {{ Form::select('tubeLabelType_id', $tubeLabelTypes) }}
    {{ Form::label('storageDestination', 'Storage Destination') }}
    {{ Form::select('storageDestination',
    [''=>'','Internal'=>'Internal','BiOS'=>'BiOS','Nexus'=>'Nexus','StorageBox'=>'StorageBox']) }}
    {{ Form::label('storageSampleType', 'Storage Sample Type') }}
    {{ Form::text('storageSampleType', null) }}
    {{ Form::label('parentSampleType_id', 'Parent Sample Type') }}
    {{ Form::select('parentSampleType_id', $sampleTypes) }}
    {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
    <x-buttonlink :href="url('/sampletypes')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>

<x-layout>
    <x-pageheader>
        Edit Sample Type
        <x-slot name='button'>
            <div x-data="deleteModal()">
                <x-modals.deleteModal />
                <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                    @click="deleteconf('sampletypes','{{$sampletype->name}}',{{$sampletype->id}})">Delete</button>
            </div>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')
    {{ Form::model($sampletype, ['route' => ['sampletypes.update', $sampletype], 'method' => 'PATCH', 'class'=>'form']) }}
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
    <select name="transferDestination[]" multiple>
        @foreach ($destinations as $key => $destination)
        <option value="{{$key}}" {{in_array($key,json_decode($sampletype->transferDestination) ?? []) ? "selected=selected" : null}}>
            {{$destination}}
        </option>
        @endforeach
    </select>
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
    {{ Form::label('active', 'Active') }}
    {{ Form::radio('active', 0) }} No {{ Form::radio('active', 1, true) }} Yes
    {{ Form::submit('Save Changes', ['class' => "w-full mt-2"]) }}
    <x-buttonlink :href="url('/sampletypes')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>

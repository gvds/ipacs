<x-layout>
    <x-pageheader>
        Allocate Sample Storage
    </x-pageheader>

    @include('layouts.message')

    <div class='flex space-x-32'>
        <div>
            <div>
                @if (!$lowstorage->isEmpty())
                <div class='text-red-600 font-semibold'>Low Storage Warning</div>
                <x-table>
                    <x-slot name="head">
                        <th>Storage Sample Type</th>
                        <th>Total Capacity</th>
                        <th>Remaining Capacity</th>
                    </x-slot>
                    @foreach ($lowstorage as $storagetype)
                    <tr class='odd:bg-gray-200'>
                        <td>{{$storagetype->storageSampleType}}</td>
                        <td>{{$storagetype->total}}</td>
                        <td>{{$storagetype->total - $storagetype->used}}</td>
                    </tr>
                    @endforeach
                </x-table>
                @endif
            </div>
            {{ Form::open(['url' => '/samplestore', 'class' => 'form', 'method' => 'POST']) }}
            <div class='mb-5 mt-2 flex items-center justify-between'>
                <span class='font-semibold'>Allow Previously Used Locations</span>
                <span class='flex border border-gray-300 bg-gray-200 px-3 py-1 ml-4 shaddow rounded space-x-3'>
                    <span>{{Form::radio('reuse[]', 0, true)}} No</span>
                    <span>{{Form::radio('reuse[]', 1)}} Yes</span>
                </span>
            </div>

            <x-table class='w-full'>
                <x-slot name='head'>
                    <th>Sample Type</th>
                    <th>Samples</th>
                    <th>Select</th>
                </x-slot>
                @foreach ($sampleSets as $sampleSet)
                @if ($sampleSet['count'] > 0)
                <tr>
                    <td>{{$sampleSet['name']}}</td>
                    <td>{{$sampleSet['count']}}</td>
                    <td>{{Form::checkbox('sampletype[]', $sampleSet['sampletype_id'])}}</td>
                </tr>
                @endif
                @endforeach
            </x-table>
            {{ Form::submit('Allocate Storage', ['class' => "w-full mt-2"]) }}
            {{ Form::close() }}
        </div>
        <div class='ml-20'>
            @if (session('unallocated'))
            <x-table>
                <x-slot name=head>
                    <th>Sample Type</th>
                    <th>Unallocated</th>
                </x-slot>
                @foreach (collect(session('unallocated')) as $sampletype=>$count)
                <tr>
                    <td>{{$sampletype}}</td>
                    <td>{{$count}}</td>
                </tr>
                @endforeach
            </x-table>
            @endif
        </div>
    </div>
</x-layout>

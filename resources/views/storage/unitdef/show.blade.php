<x-layout>
    <x-pageheader>
        Unit Definition for: {{$unitDefinition->unitType}}
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinition'>Show All Types</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    <div class='flex flex-col' x-data="deleteModal()">
        <div class='flex flex-row mb-5'>
            <div>
                <x-table>
                    <tr>
                        <th class='text-left'>Unit Type</th>
                        <td>{{$unitDefinition->unitType}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Orientation</th>
                        <td>{{$unitDefinition->orientation}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Sections</th>
                        <td>{{count($sections)}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Racks</th>
                        <td>{{$unitDefinition->racks}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Boxes</th>
                        <td>{{$unitDefinition->boxes}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Section Layout</th>
                        <td>{{$unitDefinition->sectionLayout}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Box Designation</th>
                        <td>{{$unitDefinition->boxDesignation}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Storage Type</th>
                        <td>{{$unitDefinition->storageType}}</td>
                    </tr>
                    <tr>
                        <th class='text-left'>Rack Order</th>
                        <td>{{$unitDefinition->rackOrder}}</td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold w-full'
                                @click="deleteconf('unitDefinition','{{$unitDefinition->unitType}}',{{$unitDefinition->id}})">Delete</button>
                        </td>
                    </tr>
                </x-table>
            </div>
            <div class='ml-20'>
                <div class="flex justify-between">
                    <div class='font-medium text-lg'>Sections</div>
                    <div>
                        {{ Form::open(['url' => '/section/create', 'class' => '', 'method' => 'GET']) }}
                        {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
                        {{ Form::submit('Add New Section', ['class'=>'text-sm leading-none']) }}
                        {{ Form::close() }}
                    </div>
                </div>
                <x-table>
                    <x-slot name='head'>
                        <th>Section</th>
                        <th>Rows</th>
                        <th>Columns</th>
                        <th>Boxes</th>
                        <th>Positions</th>
                    </x-slot>
                    @foreach ($sections as $section)
                    <tr class="odd:bg-gray-100">
                        <td>{{$section->section}}</td>
                        <td>{{$section->rows}}</td>
                        <td>{{$section->columns}}</td>
                        <td>{{$section->boxes}}</td>
                        <td>{{$section->positions}}</td>
                        <td>
                            <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                                @click="deleteconf('section','{{$section->section}}',{{$section->id}})">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </x-table>
            </div>
        </div>
        <div class='flex flex-col'>
            <div class='flex justify-between'>
                <div class='font-medium text-lg'>Physical Storage Units</div>
                <div>
                    {{ Form::open(['url' => '/physicalUnit/create', 'class' => '', 'method' => 'GET']) }}
                    {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
                    {{ Form::submit('Add New Unit', ['class'=>'text-sm leading-none']) }}
                    {{ Form::close() }}
                </div>
            </div>
            <x-table>
                <x-slot name='head'>
                    <th>Unit ID</th>
                    <th>Serial Number</th>
                    <th>Administrator</th>
                    <th>Available</th>
                </x-slot>
                @foreach ($physicalUnits as $physicalUnit)
                <tr class="odd:bg-gray-100">
                    <td><a class='text-blue-800 font-semibold' href='/physicalUnit/{{$physicalUnit->id}}'>{{$physicalUnit->unitID}}</a></td>
                    <td>{{$physicalUnit->serial}}</td>
                    <td>{{$physicalUnit->administrator->fullname}}</td>
                    <td>
                        <a href="/physicalUnit/{{$physicalUnit->id}}/toggleActive">
                            @if ($physicalUnit->available)
                            <svg class="h-5 w-5 text-green-600 bg-gray-200 border rounded shadow" fill="none"
                                viewBox="0 0 25 25" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            <svg class="h-5 w-5 text-red-600 bg-gray-200 border rounded shadow" fill="none"
                                viewBox="0 0 25 25" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            @endif
                        </a>
                    </td>
                    <td>
                        <x-buttonlink href="/physicalUnit/{{$physicalUnit->id}}/edit">Edit</x-buttonlink>
                    </td>
                    <td>
                        <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold leading-none'
                            @click="deleteconf('physicalUnit','{{$physicalUnit->unitID}}',{{$physicalUnit->id}})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </div>
        <x-modals.deleteModal />
    </div>


</x-layout>

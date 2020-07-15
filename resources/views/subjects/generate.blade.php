<x-layout>

    <x-pageheader>
        Generate Subject IDs
    </x-pageheader>
    
    @include('layouts.errormsg')

    {{ Form::open(['url' => '/subject', 'class' => 'form', 'method' => 'POST']) }}
    <table>
        <thead>
            <tr>
                <th>Number of New Records</th>
                <th>Arm</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ Form::selectRange('records', 1, 20, 5) }}</td>
                <td>{{ Form::select('arm', []) }}</td>
            </tr>
        </tbody>
    </table>
    {{ Form::submit('Generate') }}
    {{ Form::close() }}

</x-layout>
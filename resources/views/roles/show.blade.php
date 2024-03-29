<x-layout>

  <x-pageheader>
    Edit Permisions for Role: {{$role->name}}
  </x-pageheader>

  @include('layouts.message')

  {{ Form::open(['url' => ["role/$role->id/permissions"], 'method' => 'POST', 'class'=>'form']) }}
  <x-table class="table table-sm">
    <x-slot name='head'>
      <th>Permission</th>
    </x-slot>
    @foreach ($permissions as $permission_id => $permission)
    <tr>
      <td>{{$permission}}</td>
      <td>{{Form::checkbox($permission_id, 1, array_key_exists($permission_id,$rolepermissions)) }}</td>
    </tr>
    @endforeach
  </x-table>
  {{ Form::submit('Update Permisions', ['class' => "w-full"]) }}
  <x-buttonlink :href="url('/role')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>

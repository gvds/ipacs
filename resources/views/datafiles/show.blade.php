<x-layout>

  <x-pageheader>
    Data File Details
    @if (auth()->user()->isAbleTo(['administer-project','manage-datafiles'], $currentProject->team->name))
    <x-buttonlink href="/datafiles/{{$datafile->id}}/edit">
      Edit
    </x-buttonlink>
    @endif
  </x-pageheader>
  <div>
    <x-table>
      <tr>
        <th class='text-left'>Name</th>
        <td>{{$datafile->filename}}</td>
        @if (auth()->user()->isAbleTo(['administer-project','manage-datafiles'], $currentProject->team->name))
        <td>
          <x-delConfirm url="/datafiles/{{$datafile->id}}" />
        </td>
        @endif
      </tr>
      <tr>
        <th class='text-left'>Uploaded By</th>
        <td>{{$datafile->user->fullname}}</td>
      </tr>
      <tr>
        <th class='text-left'>Site</th>
        <td>{{$datafile->site->name}}</td>
      </tr>
      <tr>
        <th class='text-left'>Owner</th>
        <td>{{$datafile->owner}}</td>
      </tr>
      <tr>
        <th class='text-left'>Generated</th>
        <td>{{$datafile->generationDate}}</td>
      </tr>
      <tr>
        <th class='text-left'>Lab</th>
        <td>{{$datafile->lab}}</td>
      </tr>
      <tr>
        <th class='text-left'>Platform</th>
        <td>{{$datafile->platform}}</td>
      </tr>
      <tr>
        <th class='text-left'>Opperator</th>
        <td>{{$datafile->opperator}}</td>
      </tr>
      <tr>
        <th class='text-left'>File Type</th>
        <td>{{$datafile->filetype}}</td>
      </tr>
      <tr>
        <th class='text-left'>Software</th>
        <td>{{$datafile->software}}</td>
      </tr>
      <tr>
        <th class='text-left'>File Size</th>
        <td>{{round($datafile->filesize/1024**2,2)}} MB</td>
      </tr>
      <tr>
        <th class='text-left'>SHA256 Hash</th>
        <td>{{$datafile->hash}}</td>
      </tr>
      <tr>
        <th class='text-left'>Description</th>
        <td>{{$datafile->description}}</td>

      </tr>
    </x-table>
  </div>
  <x-buttonlink href='/datafiles'>Return</x-buttonlink>
</x-layout>
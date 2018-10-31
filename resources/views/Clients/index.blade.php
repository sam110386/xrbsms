    @extends('layouts.default')
    @section('content')
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ route('Client.create') }}"> Create New client</a>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th width="280px">Operation</th>
            </tr>
        @foreach ($Clients as $client)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $client->name}}</td>
            <td>{{ $client->phone}}</td>
            <td>
                <a class="btn btn-info" href="{{ route('Client.show',$client->id) }}">Show</a>
                <a class="btn btn-primary" href="{{ route('Client.edit',$client->id) }}">Edit</a>
                {!! Form::open(['method' => 'DELETE','route' => ['Client.destroy', $client->id],'style'=>'display:inline']) !!}
                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            </td>
        </tr>
        @endforeach
        </table>
        {!! $Clients->render() !!}
    @endsection
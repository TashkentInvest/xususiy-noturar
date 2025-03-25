@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>YerTola List</h1>
        <a href="{{ route('yertola.create') }}" class="btn btn-primary">Create New</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Address</th>
                    <th>Exists</th>
                    <th>Can Use</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($yertolas as $yertola)
                    <tr>
                        <td>{{ $yertola->id }}</td>
                        <td>{{ $yertola->sub_street_id }} / {{ $yertola->street_id }}</td>
                        <td>{{ $yertola->does_exists_yer_tola ? 'Mavjud' : 'Mavjud emas' }}</td>
                        <td>{{ $yertola->does_can_we_use_yer_tola ? 'Xa' : 'Yoq' }}</td>
                        <td>
                            <a href="{{ route('yertola.edit', $yertola->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('yertola.destroy', $yertola->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

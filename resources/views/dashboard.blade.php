@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if(session('revoked'))
                    @include('includes.token-revoked')
                    @endif

                    {{-- 5 latest builds --}}
                    <h5>Latest 5 builds</h5>

                    @if($builds)
                    <table class="table">
                        <thead>
                            <th>Branch</th>
                            <th>Commit Hash</th>
                            <th>Successful</th>
                            <th>Created at</th>
                        </thead>

                        <tbody>
                            @foreach($builds as $build)
                            <tr>
                                <td>{{ $build->branch }}</td>
                                <td title="{{ $build->commit_hash }}">{{ $build->displayHash() }}</td>
                                <td>{{ $build->successful ? 'Yes' : 'No' }}</td>
                                <td title="{{ $build->created_at }}">{{ $build->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info">
                        No builds are in the system.
                    </div>
                    @endif

                    <hr>

                    {{-- List projects --}}
                    <h5>All projects</h5>

                    @if($tokens)
                    <table class="table">
                        <thead>
                            <th>Name</th>
                            <th></th>
                        </thead>

                        <tbody>
                            @foreach ($tokens as $token)
                            <tr>
                                <td>{{ $token->name }}</td>
                                <td>
                                    <form action="{{ route('token.destroy', $token->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf

                                        <input type="submit" value="Revoke" class="link text-danger">
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info">
                        No projects have been created.
                    </div>
                    @endif

                    {{-- Create new project --}}
                    <a href="{{ route('token.create') }}" class="btn btn-outline-secondary">New project</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

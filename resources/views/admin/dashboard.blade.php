@extends('layouts.app')
@section('title', 'Admin – Avaleht')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
        </div>
@endif
<h1 class="mb-4">{{ Auth::user()->name }} paneel</h1>

<div class="row g-4">

    {{-- Needs action --}}
    <div class="col-md-6">
        <h4>Vajab ülevaatust</h4>
        @if($needsAction->isEmpty())
            <div class="text-muted">Kõik korras.</div>
        @else
            <p class="text-muted fw-bold">Kokku vajab ülevaatamist: <span class="text-primary">{{$needsAction->count()}}</span> postitust.</p>
                <ul class="list-group">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Pealkiri</th>
                            <th>Autor</th>
                            <th>Uuendatud</th>
                            <th>Staatus</th>
                            <th>Muutma</th>
                        </tr>
                </thead>
                <tbody>
                    @foreach($needsAction->take(5) as $post)
                    <tr>
                        <td>{{$post->title}}</td>
                        <td>{{$post->author->name}}</td>
                        <td>{{$post->updated_at->format('d.m.Y H:i')}}</td>
                        <td>{{$post->status}}</td>
                        <td><a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-square-up-right"></i>
                        Ava
                    </a></td>
                    </tr>
                  @endforeach  
                </tbody>
                </table>
                
            </ul>
        @endif
    </div>

    {{-- Scheduled --}}
    <div class="col-md-6">
        <h4>Planeeritud</h4>
        @if($scheduled->isEmpty())
            <div class="text-muted">Pole planeeritud postitusi.</div>
        @else
            <p class="text-muted fw-bold">Kokku planeeritud postitusi: <span class="text-primary">{{$scheduled->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Pealkiri</th>
                                <th>Autor</th>
                                <th>Avaldamise aeg</th>
                                <th>Muutma</th>
                            </tr>
                    </thead>
                    <tbody>
                @foreach($scheduled->take(5) as $post)
                    <tr>
                        <td>{{$post->title}}</td>
                        <td>{{$post->author->name}}</td>
                        <td>{{$post->published_at->format('d.m.Y H:i')}}</td>
                        <td><a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-square-up-right"></i>
                        Ava
                    </a></td>
                    </tr>
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div>

    {{-- Recent published --}}
    <div class="col-md-6">
        <h4>Hiljuti avaldatud</h4>
        @if($recent->isEmpty())
            <div class="text-muted">Hiljuti pole avaldatud.</div>
        @else
            <p class="text-muted fw-bold">Kokku hiljuti avaldatud postitusi: <span class="text-primary">{{$recent->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Pealkiri</th>
                                <th>Autor</th>
                                <th>Avaldamise aeg</th>
                            </tr>
                    </thead>
                    <tbody>
                    @foreach($recent->take(5) as $post)
                    <tr>
                        <td>{{$post->title}}</td>
                        <td>{{$post->author->name}}</td>
                        <td>{{$post->published_at->format('d.m.Y H:i')}}</td>
                    </tr>
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div>    

    {{-- Pending comments --}}
    <div class="col-md-6">
        <h4>Kommentaarid – ootel</h4>
        @if($pendingComments->isEmpty())
            <div class="text-muted">Pole ootel kommentaare.</div>
        @else
            <p class="text-muted fw-bold">Kokku ootel kommentaari: <span class="text-primary">{{$pendingComments->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Autor</th>
                                <th>Loomise aeg</th>
                                <th>Kommentaar</th>
                                <th>Muuda staatust</th>
                                @role('Admin')
                                <th>Kustuta</th>
                                @endrole
                            </tr>
                    </thead>
                    <tbody>
                  @foreach($pendingComments->take(5) as $comment)
                    <tr>
                        <td>{{$comment->author->name ?? 'Anonüümne'}}</td>
                        <td>{{$comment->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ Str::limit($comment->body, 30) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.comments.updateStatus', $comment) }}" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')

                                {{-- Rippmenüü --}}
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                                    <option value="">Kõik</option>
                                    @foreach (['approved','hidden','spam','pending'] as $st)
                                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                </form>
                                </td>

                                {{-- Kustuta nupp ainult adminile --}}
                                    @can('delete', $comment)
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endcan
                    </tr>
                 
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div> 

    {{-- Arhiveeritud postitused --}}
    <div class="col-md-6">
        <h4>Arhiveeritud postitused</h4>
        @if($recent->isEmpty())
            <div class="text-muted">Arhiveeritud postitusi pole.</div>
        @else
            <p class="text-muted fw-bold">Kokku arhiveeritud postitusi: <span class="text-primary">{{$archivedPosts->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Pealkiri</th>
                                <th>Autor</th>
                                <th>Uuendamise aeg</th>
                                <th>Staatus</th>
                                <th>Muuda</th>
                            </tr>
                    </thead>
                    <tbody>
                    @foreach($archivedPosts->take(5) as $post)
                    <tr>
                        <td>{{$post->title}}</td>
                        <td>{{$post->author->name}}</td>
                        <td>{{$post->updated_at->format('d.m.Y H:i')}}</td>
                        <td>{{$post->status}}</td>
                        <td><a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i>
                        Muutma
                    </a></td>
                    </tr>
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div>         

     {{-- Hüljatud kommentaarid --}}
    <div class="col-md-6">
        <h4>Orvuks jäänud kommentaarid</h4>
        @if($orphanComments->isEmpty())
            <div class="text-muted">Orvuks jäänud kommentaare pole.</div>
        @else
            <p class="text-muted fw-bold">Kokku orvuks jäänud kommentaare: <span class="text-danger">{{$orphanComments->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Kommentaar</th>
                                <th>Autor</th>
                                <th>Uuendamise aeg</th>
                                <th>Postitus</th>
                                @role('Admin')
                                <th>Kustuta</th>
                                @endrole
                            </tr>
                    </thead>
                    <tbody>
                    @foreach($orphanComments->take(5) as $comment)
                    <tr>
                        <td>{{$comment->body}}</td>
                        <td>{{$comment->author->name}}</td>
                        <td>{{$comment->updated_at->format('d.m.Y H:i')}}</td>
                        <td>
                                {{-- 1) Postitus jäädavalt kustutatud --}}
                                @if(!$comment->post)
                                    <span class="text-danger">Postitus puudub</span>

                                {{-- 2) Postitus prügikastis (soft deleted) --}}
                                @else($comment->post->trashed())
                                    <span class="text-danger">Postitus prügikastis</span>
                                @endif
                            </td>

                            {{-- Kustuta nupp ainult adminile --}}
                                
                                    @can('delete', $comment)
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endcan
                    </tr>
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div>

    {{-- Prügikast postitustele --}}
    <div class="col-md-6">
        <h4>Prügikast (postitused)</h4>
        @if($trashedPosts->isEmpty())
            <div class="text-muted">Prügikast on tühi.</div>
        @else
            <p class="text-muted fw-bold">Kokku prügikastis postitusi: <span class="text-danger">{{$trashedPosts->count()}}</span>.</p>
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Pealkiri</th>
                                <th>Autor</th>
                                <th>Loodud</th>
                                <th>Muudetud</th>
                                <th>Kustutatud</th>
                                <th>Taasta</th>
                                @role('Admin')
                                <th>Kustuta</th>
                                @endrole
                            </tr>
                    </thead>
                    <tbody>
                    @foreach($trashedPosts->take(5) as $post)
                    <tr>
                        <td>{{$post->title}}</td>
                        <td>{{$post->author->name}}</td>
                        <td>{{$post->created_at->format('d.m.Y H:i')}}</td>
                        <td>{{$post->updated_at->format('d.m.Y H:i')}}</td>
                        <td>{{$post->deleted_at->format('d.m.Y H:i')}}</td>
                        <td><a href="#" class="btn btn-sm btn-primary"><i class="fa-solid fa-wand-magic"></i>
                        Taasta
                    </a></td>
                            {{-- Kustuta nupp ainult adminile --}}
                                
                                    @role('Admin')
                                    <td class="text-center">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                    </td>
                                    @endrole
                               
                    </tr>
                  @endforeach  
                </tbody>
                </table>
        @endif
    </div>                  

</div>
@endsection

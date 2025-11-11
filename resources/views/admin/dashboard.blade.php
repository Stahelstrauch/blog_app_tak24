@extends('layouts.app')
@section('title', 'Admin – Avaleht')

@section('content')
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
                    @foreach($needsAction->sortByDesc('updated_at')->take(5) as $post)
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
            <ul class="list-group">
                @foreach($scheduled as $post)
                <li class="list-group-item">
                    <strong>{{ $post->title }}</strong>
                    <br>
                    <small>Publitseeritakse: {{ $post->published_at->format('d.m.Y H:i') }}</small>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Recent published --}}
    <div class="col-md-6">
        <h4>Hiljuti avaldatud</h4>
        @if($recent->isEmpty())
            <div class="text-muted">Hiljuti pole avaldatud.</div>
        @else
            <ul class="list-group">
                @foreach($recent as $post)
                <li class="list-group-item">
                    <strong>{{ $post->title }}</strong>
                    <br>
                    <small>{{ $post->author->name }} – {{ $post->published_at->format('d.m.Y H:i') }}</small>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Pending comments --}}
    <div class="col-md-6">
        <h4>Kommentaarid – ootel</h4>
        @if($pendingComments->isEmpty())
            <div class="text-muted">Pole ootel kommentaare.</div>
        @else
            <ul class="list-group">
                @foreach($pendingComments as $comment)
                @if($comment->post)
                <li class="list-group-item">
                    {{ Str::limit($comment->comment, 60) }}<br>
                    <small>
                        {{ $comment->author->name ?? 'Anonüümne' }},
                        {{ $comment->created_at->format('d.m.Y H:i') }},
                        post: <a href="{{ route('admin.posts.edit', $comment->post) }}">{{ $comment->post->title }}</a>
                    </small>
                </li>
                @endif
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection

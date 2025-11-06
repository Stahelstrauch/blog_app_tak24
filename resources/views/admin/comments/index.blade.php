@extends('layouts.app')
@section('title','Kommentaarid – Admin')

@section('content')

@if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
<h1>Kommentaaride modereerimine</h1>

<form method="get" class="row" style="align-items:center;margin-bottom:12px">
    <label for="status" class="muted">Staatus</label>
    <select name="status" id="status" onchange="this.form.submit()" style="margin-left:8px">
        <option value="">Kõik</option>
        @foreach (['pending','approved','hidden','spam'] as $st)
            <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
        @endforeach
    </select>
</form>

@if($comments->isEmpty())
    <p class="muted">—</p>
@else
    <div class="stack">
        @foreach ($comments as $c)
            <div style="padding:.75rem;border:1px solid #ddd;border-radius:6px">
                <div class="muted">
                    {{ $c->author?->name ?? '—' }}
                    • {{ $c->created_at->format('d.m.Y H:i') }}
                    • [{{ $c->status }}]
                    • Postitus:
                    @if($c->post)
                        <a href="{{ route('blog.show',$c->post->slug) }}" target="_blank">{{ $c->post->title }}</a>
                    @else
                        —
                    @endif
                </div>
                <div style="margin-top:.5rem">{{ $c->body }}</div>

                <div class="row" style="margin-top:.5rem">
                    @foreach (['approved'=>'Kinnita','hidden'=>'Peida','spam'=>'Spam','pending'=>'Ootele'] as $status => $label)
                        <form action="{{ route('admin.comments.updateStatus',$c) }}" method="post">
                            @csrf @method('patch')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button class="btn">{{ $label }}</button>
                        </form>
                    @endforeach
                    <form action="{{ route('admin.comments.destroy',$c) }}" method="post" onsubmit="return confirm('Kustuta kommentaar?')">
                        @csrf @method('delete')
                        <button class="btn">Kustuta</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:12px">
        {{ $comments->links() }}
    </div>
@endif
@endsection

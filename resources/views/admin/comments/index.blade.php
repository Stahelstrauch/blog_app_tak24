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
    <label for="status" class="muted">Sorteeri staatuse järgi:</label>
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
                    <table class="table table-bordered table-striped aligned-middle">
                        <thead>
                            <tr>
                                <th>Autor</th>
                                <th>Pealkiri</th>
                                <th>Loomise aeg</th>
                                <th>Staatus</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <td>{{ $c->author?->name ?? '—' }}</td>
                            <td>
                                @if(!$c->post)
                                    —
                                @elseif($c->post->trashed())
                                    —
                                @else
                                    <a href="{{ route('blog.show', $c->post->slug) }}" target="_blank">
                                        {{ $c->post->title }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ $c->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $c->status }}</td>
                       </tbody> 
                </table>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kommentaar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td>{{ $c->body }}</td>
                    </tbody>
                </table>
                
                <div class="d-flex flex-wrap gap-1" style="margin-top:.5rem">
                    @foreach (['approved'=>'Kinnita','hidden'=>'Peida','spam'=>'Spam','pending'=>'Ootele'] as $status => $label)
                        <form action="{{ route('admin.comments.updateStatus',$c) }}" method="post">
                            @csrf @method('patch')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button class="btn btn-outline-primary">{{ $label }}</button>
                        </form>
                    @endforeach
                    <form action="{{ route('admin.comments.destroy',$c) }}" method="post" onsubmit="return confirm('Kustuta kommentaar?')">
                        @csrf @method('delete')
                        <button class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                    </form>
                    </div>
                </div>
        @endforeach
    </div>

    <div style="margin-top:12px">
        {{ $comments->links() }}
    </div>
@endif
</div>
@endsection

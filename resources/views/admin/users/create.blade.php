@extends('layouts.app')
@section('title','Uus kasutaja – Admin')

@section('content')
<h1>Uus kasutaja</h1>

<form action="{{ route('admin.users.store') }}" method="post" class="stack">
    @csrf
    <label>Nimi
        <input type="text" name="name" value="{{ old('name') }}" required>
    </label>

    <label>E-post
        <input type="email" name="email" value="{{ old('email') }}" required>
    </label>

    <label>Parool
        <input type="password" name="password" required minlength="8">
    </label>
    <label>Korda parooli
        <input type="password" name="password_confirmation" required minlength="8">
    </label>

    <label>Roll
        <select name="role_id" required>
            @foreach($roles as $id=>$name)
                <option value="{{ $id }}" @selected(old('role_id')==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </label>

    <button class="btn btn-primary">Loo</button>
    <a class="btn" href="{{ route('admin.users.index') }}">Tagasi</a>
</form>
@endsection

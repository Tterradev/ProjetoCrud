@extends('layouts.default')

@section('content')

<div class="page page-user page-form">

    @include('components.alert')
    
    <h1> Formulário de usuários </h1>

    <form method="POST" action="{{ url('/usuarios') }}">

        @csrf
        @method($user->id ? 'PUT':'POST')

        <input type="hidden" name="id" value="{{ $user->id }}">

        <div class="form-group">

            <label for="">Nome</label>
            <input type="text" name="name" class="form-control" value="{{ old ('name',$user->name) }}" maxlength="50" required></input>

        </div>
        

        <div class="form-group">

            <label for="">E-mail</label>
            <input type="email" name="email" class="form-control" value="{{ old ('email',$user->email) }}" maxlength="50" required></input>

        </div>

        <div class="form-group">

            <label for="">Senha</label>
            <input type="password" name="password" class="form-control" minlength="8" maxlength="16"  {{ !$user->id ? 'required' : '' }}></input>

        </div>

        <button type="submit">Enviar</button>

        <a href="{{ url('/usuarios') }}">Voltar</a>

    </form>

</div>

    <h1>hello word</h1>

@endsection

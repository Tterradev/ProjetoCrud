@extends('layouts.default')

@section('content')

<div class="page page-user page-index">

    @include('components.alert')
    
    <h1> Listagem de usuários </h1>

    <div class="table-responsive">
        
        <table class="table table-striped"> 

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

                @foreach ($list as $user)

                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ url('/usuario/'.$user->id.'/editar') }}">Editar usuario</a>
                            <button>Remover usuario</button>
                        </td>
                    </tr>
                    
                @endforeach

            </tbody>

        </table>

    </div>

    <a href="{{ url('/usuarios/cadastrar') }}">Cria um novo usuário</a>

</div>

    <h1>hello word</h1>

@endsection

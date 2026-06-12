@extends('layouts.default')

@section('content')

<div class="page page-user page-index">

    @include('components.alert')
    
    <h1> Listagem de usuários </h1>

    <form method="GET "action="{{ url('/usuarios') }}">

        <div class="form-group">
            <label for="">Nome</label>
            <input type="text" name="name" class="form-control"/>
        </div>

        <div class="form-group">
            <label for="">Email</label>
            <input type="text" name="email" class="form-control"/>
        </div>
        
        <div class="form-group">
            <label for="">Qtde. de registros por página</label>
            <select name="limit" id="">
                <option value="10">10</options>
                <option value="25">25</options>
                <option value="50">50</options>
                <option value="100">100</options>
            </select>    
        </div>

        <button type="submit">Atualizar</button>

    </form>

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

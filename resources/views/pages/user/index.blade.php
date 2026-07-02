@extends('layouts.default')

@section('content')

    <div class="page page-user page-index">

        @include('components.alert')
        
        <h1> Listagem de usuários </h1>

        <form method="GET "action="{{ url('/usuarios') }}">

            <div class="form-group">
                <label for="">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ Request::get('name') }}"/>
            </div>

            <div class="form-group">
                <label for="">Email</label>
                <input type="text" name="email" class="form-control" value="{{ Request::get('email') }}"/>
            </div>
            
            @include('components.limit')

            <a href="{{ url('/usuarios') }}">Limpar Filtro</a>
            
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
                                <form action="{{ url('/usuario') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <button>Remover usuario</button>
                                </form>
                            </td>
                        </tr>
                        
                    @endforeach

                </tbody>

            </table>

            {{  $list->links() }}

        </div>

        <a href="{{ url('/usuarios/cadastrar') }}">Cria um novo usuário</a>

    </div>

        <h1>hello word</h1>

@endsection

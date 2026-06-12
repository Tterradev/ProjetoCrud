@php
    
    $success = session('sucess');

    $error = session('error');

    $errorsList = $errors->all();

@endphp

@if ($success)

    <div class="alert alert-sucess" role="alert">
        {{ $success }}
    </div>

@elseif ($error)

    <div class="alert alert-danger role="alert">
        {{ $error }}
    </div>

@elseif (isset($errors))

    <div class="alert alert-danger role="alert">
        <ul>
            @foreach ($errors as $errors)
                <li>{{ $errors }}</li>
            @endforeach
        </ul>
    </div>

@endif

@php
    
    $success = session('success');

    $error = session('error');

    $errorsList = $errors->all();

@endphp

@if ($success)

    <div class="alert alert-success" role="alert">
        {{ $success }}
    </div>

@elseif ($error)

    <div class="alert alert-danger" role="alert">
        {{ $error }}
    </div>

@elseif ($errorsList)

    <div class="alert alert-danger role="alert">
        <ul>
            @foreach ($errorsList as $errorsMessage)
                <li>{{ $errors }}</li>
            @endforeach
        </ul>
    </div>

@endif

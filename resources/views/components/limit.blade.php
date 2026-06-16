@php

    $list = [10,25,50,100];

    $selected = Request::get('limit', $list [0]);

@endphp

<div class="form-group">

    <label for="">Qtde. de registros por página</label>

    <select name="limit" id="">

        @foreach ($list as $value)

             <option value="{{ $value }}" {{ $value == $selected ? 'selected' : '' }}>{{ $value }}</options>

        @endforeach

    </select>    

</div>
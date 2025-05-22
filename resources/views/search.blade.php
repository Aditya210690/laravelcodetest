<!DOCTYPE html>
<html>
<head>
    <title>SerpApi Search</title>
</head>
<body>
    <h1>Search SerpApi</h1>

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('search.results') }}">
    @csrf
    @for ($i = 0; $i < 5; $i++)
        <div>
            <label>Search Query {{ $i + 1 }}</label>
            <input type="text" name="queries[]" maxlength="255" required>
        </div>
    @endfor
    <button type="submit">Search</button>
</form>
</body>
</html>

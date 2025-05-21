<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SerpApi Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
<div class="container">
    <h2 class="mb-4">Google Search via SerpApi</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('search.perform') }}">
        @csrf
        @for ($i = 0; $i < 5; $i++)
            <div class="mb-2">
                <input type="text" name="queries[]" class="form-control" placeholder="Search Query #{{ $i + 1 }}"
                       value="{{ old('queries')[$i] ?? '' }}">
            </div>
        @endfor
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    @if(isset($results) && count($results))
        <hr>
        <h4>Results</h4>
        <a href="{{ route('search.export') }}" class="btn btn-success mb-3">Export CSV</a>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Query</th>
                <th>Title</th>
                <th>Link</th>
                <th>Snippet</th>
            </tr>
            </thead>
            <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result['query'] }}</td>
                    <td>{{ $result['title'] }}</td>
                    <td><a href="{{ $result['link'] }}" target="_blank">Link</a></td>
                    <td>{{ $result['snippet'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>

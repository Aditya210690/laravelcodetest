<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
</head>
<body>
    <h2>Search Results</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Query</th>
                <th>Title</th>
                <th>Link</th>
                <th>Snippet</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allResults as $result)
                <tr>
                    <td>{{ $result['query'] }}</td>
                    <td>{{ $result['title'] }}</td>
                    <td><a href="{{ $result['link'] }}" target="_blank">{{ $result['link'] }}</a></td>
                    <td>{{ $result['snippet'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('search.export') }}">Download CSV</a>
</body>
</html>

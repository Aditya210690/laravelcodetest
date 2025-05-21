<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchController extends Controller
{
    protected $results = [];

    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'queries' => 'required|array|min:1|max:5',
            'queries.*' => 'required|string|max:255',
        ]);

        $results = [];
        $apiKey = config('services.serpapi.key');

        foreach ($request->queries as $query) {
            try {
                $response = Http::get('https://serpapi.com/search.json', [
                    'q' => $query,
                    'api_key' => $apiKey,
                    'engine' => 'google',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $organic_results = $data['organic_results'] ?? [];

                    foreach ($organic_results as $result) {
                        $results[] = [
                            'query'   => $query,
                            'title'   => $result['title'] ?? 'N/A',
                            'link'    => $result['link'] ?? 'N/A',
                            'snippet' => $result['snippet'] ?? 'N/A',
                        ];
                    }
                } else {
                    return back()->withErrors(['msg' => 'API error: ' . $response->status()]);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['msg' => 'Exception: ' . $e->getMessage()]);
            }
        }

        session(['results' => $results]);
        return view('search', compact('results'));
    }

    public function export()
    {
        $results = session('results', []);

        if (empty($results)) {
            return redirect()->route('search.index')->withErrors(['msg' => 'No results to export.']);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="search_results.csv"',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Query', 'Title', 'Link', 'Snippet']);

            foreach ($results as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}



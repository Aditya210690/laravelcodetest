<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchController extends Controller
{
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

        $apiKey = config('services.serpapi.key');
        $allResults = [];

        foreach ($request->queries as $query) {
            $response = Http::get('https://serpapi.com/search', [
                'q' => $query,
                'api_key' => $apiKey,
                'engine' => 'google',
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $organicResults = $json['organic_results'] ?? [];

                foreach ($organicResults as $result) {
                    $allResults[] = [
                        'query' => $query,
                        'title' => $result['title'] ?? '',
                        'link' => $result['link'] ?? '',
                        'snippet' => $result['snippet'] ?? '',
                    ];
                }
            }
        }

        // Save results to session for export
        session(['search_results' => $allResults]);

        return view('results', compact('allResults'));
    }

    public function export()
    {
        $results = session('search_results', []);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="search_results.csv"',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Query', 'Title', 'Link', 'Snippet']);

            foreach ($results as $row) {
                fputcsv($file, [$row['query'], $row['title'], $row['link'], $row['snippet']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


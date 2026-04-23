<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('es:search {query}')]
#[Description('Command description')]
class EsSearchCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //опечатки
        //"aplpe mouse"
        //"logitec keybord"
        //"wirless hedphones"

        //синонимы
        //"notebook"
        //"ultrabook"
        //"display"

        $query = $this->argument('query');

        $start = microtime(true);

        $results = Product::select('*')
            ->selectRaw("
        ts_rank(
            to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(description,'')),
            websearch_to_tsquery('simple', ?)
        ) as rank
    ", [$query])
            ->whereRaw("
        to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(description,''))
        @@ websearch_to_tsquery('simple', ?)
    ", [$query])
            ->orderByDesc('rank')
            ->limit(20)
            ->get();

        $time = microtime(true) - $start;

        $this->info("PG FULLTEXT: {$time}");

        $client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();

        $start = microtime(true);

        $result = $client->search([
            'index' => 'products',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^3', 'title.autocomplete', 'description'],
                        'fuzziness' => 'AUTO',
                        'type' => 'best_fields',
                        'tie_breaker' => 0.3
                    ]
                ],
                'size' => 20
            ]
        ]);

//        $time = microtime(true) - $start;
//
//        $this->info("ES: {$time}");
//
//        $this->info("PG IDs:");
//        $this->line(implode(',', $results->pluck('id')->toArray()));
//
        $this->info("ES IDs:");
        $this->line(implode(',', array_column($result['hits']['hits'], '_id')));
//
//        $pgIds = $results->pluck('id')->toArray();
//        $esIds = array_column($result['hits']['hits'], '_id');
//
//        $intersection = array_intersect($pgIds, $esIds);
//
//        $this->info("Intersection: " . count($intersection) . "/20");
    }
}

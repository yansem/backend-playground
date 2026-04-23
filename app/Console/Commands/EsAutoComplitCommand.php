<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('es:auto-comp {query}')]
#[Description('Command description')]
class EsAutoComplitCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->argument('query');

        $client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();

        $params = [
            'index' => 'products',
            'body' => [
                'query' => [
                    'match' => [
                        'title.autocomplete' => [
                            'query' => $query
                        ]
                    ]
                ],
                '_source' => ['title'],
                'size' => 5
            ]
        ];

        $result = $client->search($params);

        $this->info("ES IDs:");
        $this->line(implode(',', array_column($result['hits']['hits'], '_id')));

        foreach ($result['hits']['hits'] as $hit) {
            $this->line($hit['_source']['title']);
        }
    }
}

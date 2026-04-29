<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('es:filter {query}')]
#[Description('Command description')]
class EsFilterCommand extends Command
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

        $result = $client->search([
            'index' => 'products',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query' => $query,
                                    'fields' => ['title^3', 'description'],
                                    'fuzziness' => 'AUTO'
                                ]
                            ]
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'price' => [
                                        'gte' => 100,
                                        'lte' => 1000
                                    ]
                                ]
                            ],
                            [
                                'term' => [
                                    'category_slug' => 'laptops'
                                ]
                            ]
                        ]
                    ]
                ],
                'aggs' => [
                    'categories' => [
                        'terms' => [
                            'field' => 'category'
                        ]
                    ],
                    'price_stats' => [
                        'stats' => [
                            'field' => 'price'
                        ]
                    ]
                ]
            ]
        ]);

        $this->info("ES IDs:");
        $this->line(implode(',', array_column($result['hits']['hits'], '_id')));

        foreach ($result['hits']['hits'] as $hit) {
            $this->line($hit['_source']['title']);
        }

        dd($result);
    }
}

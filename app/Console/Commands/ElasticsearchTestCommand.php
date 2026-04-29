<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('es:test')]
#[Description('Test Elasticsearch')]
class ElasticsearchTestCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();

        // 1. Удаляем индекс (если существует)
        if ($client->indices()->exists(['index' => 'products'])->asBool()) {
            $client->indices()->delete(['index' => 'products']);
        }

        // 2. Создаём индекс с анализатором и синонимами
        $params = [
            'index' => 'products',
            'body' => [
                'settings' => [
                    'analysis' => [
                        'tokenizer' => [
                            'autocomplete_tokenizer' => [
                                'type' => 'edge_ngram',
                                'min_gram' => 2,
                                'max_gram' => 20,
                                'token_chars' => ['letter', 'digit']
                            ]
                        ],
                        'filter' => [
                            'my_synonyms' => [
                                'type' => 'synonym',
                                'synonyms' => [
                                    'laptop, notebook, ultrabook',
                                    'mouse, pointer',
                                    'monitor, display, screen',
                                    'headset, headphones',
                                ]
                            ]
                        ],
                        'analyzer' => [
                            'my_analyzer' => [
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'my_synonyms'
                                ]
                            ],
                            'autocomplete' => [
                                'type' => 'custom',
                                'tokenizer' => 'autocomplete_tokenizer',
                                'filter' => ['lowercase']
                            ],
                            'autocomplete_search' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'my_analyzer',
                            'fields' => [
                                'autocomplete' => [
                                    'type' => 'text',
                                    'analyzer' => 'autocomplete',
                                    'search_analyzer' => 'autocomplete_search'
                                ]
                            ]
                        ],
                        'description' => [
                            'type' => 'text',
                            'analyzer' => 'my_analyzer'
                        ],
                        'price' => [
                            'type' => 'float'
                        ],
                        'category' => [
                            'type' => 'keyword'
                        ],
                        'category_slug' => [
                            'type' => 'keyword'
                        ],
                    ]
                ]
            ]
        ];

        $client->indices()->create($params);

        // 3. Индексация данных
        Product::chunk(1000, function ($products) use ($client) {

            $params = ['body' => []];

            foreach ($products as $product) {
                $params['body'][] = [
                    'index' => [
                        '_index' => 'products',
                        '_id' => $product->id,
                    ]
                ];

                $params['body'][] = [
                    'title' => $product->title,
                    'description' => $product->description ?? '',
                    'price' => (float) $product->price,
                    'category' => $product->category,
                    'category_slug' => $product->category_slug,
                ];
            }

            $client->bulk($params);
        });

        $this->info('Index recreated with synonyms and data indexed.');
    }
}

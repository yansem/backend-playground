<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:test-command')]
#[Description('Command description')]
class TestCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $r = Product::query()
            ->whereIn('id', [899,2825,4763,6917,10295,11473,13542,15430,17530,27])
            ->each(fn($q) => dump($q->price));
//        dd($r);
    }
}

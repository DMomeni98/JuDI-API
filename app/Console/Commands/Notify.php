<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task_reminder:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify members their upcomming tasks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // DB::table('cards')->orderBY('id')->chunk(100, function($cards){
        //     foreach($cards as $card){
        //         if(time() == strtotime("+1 hour",$card->due)){

        //         }
        //     }
        // });
        DB::table('cards')
            ->where('due', '<', strtotime("+1 hour", time()));
    }
}

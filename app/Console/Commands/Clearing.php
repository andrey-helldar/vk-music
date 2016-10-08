<?php

namespace VKMUSIC\Console\Commands;

use Illuminate\Console\Command;
use VKMUSIC\VkResponse;

class Clearing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing database tables';

    /**
     * Create a new command instance.
     *
     * Clearing constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $methods = [
            'audio.add',
        ];
        VkResponse::whereIn('method', $methods)->delete();
    }
}

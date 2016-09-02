<?php

namespace VKMUSIC\Console\Commands;

use Illuminate\Console\Command;
use VKMUSIC\VkQueue;

class RequestVk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Requests to VK API.';

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
     * @return mixed
     */
    public function handle()
    {
        $items = VkQueue::groupBy('access_token')->take(25)->get();
    }
}

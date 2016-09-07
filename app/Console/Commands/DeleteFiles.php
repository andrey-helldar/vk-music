<?php

namespace VKMUSIC\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use VKMUSIC\VkFile;

class DeleteFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:files-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired files';

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
        $items = VkFile::where('expired_at', '<', Carbon::now())->get();

        if (!$items->count()) {
            return;
        }

        $files = [];

        foreach ($items as $item) {
            $files[] = $item->filename;
            $item->delete();
        }

        \Storage::disk('mp3')->delete($files);
    }
}

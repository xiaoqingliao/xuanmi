<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Ali\AliVideoTrack;

class VideoTrackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:track';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $track = new AliVideoTrack();
        $r = $track->track([
            'Duration' => 180,
            'VideoTracks' => [
                [
                    'Duration' => 180,
                    'VideoTrackClips' => [
                        [
                            'VideoId' => '7efbd1ee09af4da3a9367ef7262d08bc',
                            'In' => 0,
                            'Out' => 60,
                        ],
                    ],
                ],
            ],
        ]);
        $this->comment(json_encode($r, JSON_UNESCAPED_UNICODE));
    }
}

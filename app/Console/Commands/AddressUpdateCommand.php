<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\miniapp\DituService;
use App\Models\Member;

class AddressUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:update';

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
        $ditu = new DituService();
        $members = Member::where('address', '<>', '')->get();
        foreach($members as $member) {
            if ($member->lat != '' && $member->lng != '') continue;
            if (empty($member->address)) continue;
            $this->comment('update member latlng:' . $member->id);
            $r = $ditu->reverse($member->address);
            if ($r != null) {
                $lat = $r['lat'];
                $lng = $r['lng'];

                $member->lat = $lat;
                $member->lng = $lng;
                $member->save();
                $this->comment('member latlng updated:' . $member->id);
            }
        }
    }
}

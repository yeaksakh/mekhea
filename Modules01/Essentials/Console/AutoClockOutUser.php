<?php

namespace Modules\Essentials\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Essentials\Entities\EssentialsAttendance;

class AutoClockOutUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'pos:autoClockOutUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto clock out user for a given time';

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
    // public function handle()
    // {
    //     $attendances = EssentialsAttendance::join('essentials_shifts as es', 'essentials_attendances.essentials_shift_id', 'es.id')
    //         ->where('es.is_allowed_auto_clockout', 1)
    //         ->whereNull('essentials_attendances.clock_out_time')
    //         ->whereBetween('es.auto_clockout_time', [Carbon::now()->toTimeString(), Carbon::now()->addMinutes(30)->toTimeString()])
    //         ->update(['clock_out_time' => Carbon::now()->toDateTimeString()]);
    // }


    public function handle()
    {
        $now = Carbon::now();

        EssentialsAttendance::join('essentials_shifts as es', 'essentials_attendances.essentials_shift_id', 'es.id')
            ->where('es.is_allowed_auto_clockout', 1)
            ->whereNotNull('essentials_attendances.clock_in_time')
            ->whereDate('essentials_attendances.clock_in_time', $now->toDateString())
            ->whereNull('essentials_attendances.clock_out_time')
            ->whereRaw('TIME(es.auto_clockout_time) <= ?', [$now->format('H:i:s')])
            ->update(['clock_out_time' => $now]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Report as Reporting;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчет данных для отчетов';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        while (true){

            Reporting::allErrors();

            $date = (new \DateTime())->modify('+3 hours');
            Reporting::countErrorsByDay($date, 'errors_today');

            $date = (new \DateTime())->modify('+3 hours')->modify('-1 day');
            Reporting::countErrorsByDay($date, 'errors_yesterday');

            $date = (new \DateTime())->modify('+3 hours')->modify('-2 day');
            Reporting::countErrorsByDay($date, 'errors_2days');

            $date = (new \DateTime())->modify('+3 hours')->modify('-3 day');
            Reporting::countErrorsByDay($date, 'errors_3days');

            $date = (new \DateTime())->modify('+3 hours')->modify('-4 day');
            Reporting::countErrorsByDay($date, 'errors_4days');

            $date = (new \DateTime())->modify('+3 hours')->modify('-5 day');
            Reporting::countErrorsByDay($date, 'errors_5days');

            $date = (new \DateTime())->modify('+3 hours')->modify('-6 day');
            Reporting::countErrorsByDay($date, 'errors_6days');

            $date = (new \DateTime())->modify('+3 hours')->modify('-7 day');
            Reporting::countErrorsByDay($date, 'errors_7days');

            $now = (new \DateTime())->modify('+3 hours');
            Reporting::countErrorsByWeek($now, 'errors_week');
            Reporting::countErrorsByMonth($now, 'errors_week');

            sleep(60);
        }

    }
}

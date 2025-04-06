<?php

namespace App\Actions;

use App\Models\Error;
use App\Models\Reporting;
use Illuminate\Support\Facades\DB;

class Report
{

    public static function allErrors(): void
    {
        $data = Error::query()->select(['name', 'team', DB::raw('COUNT(guid) as count')])->groupBy(['team', 'name'])->get();
        foreach ($data as $row){
            Reporting::write($row->team, $row->name, 'all_errors', $row->count);
        }
    }

    public static function countErrorsByDay(\DateTime $date, string $category): void
    {
        $start = $date->modify('today')->format('Y-m-d H:i:s');
        $end = $date->format('Y-m-d').' 23:59:59';
        $data = Error::query()->select(['name', 'team', DB::raw('COUNT(guid) as count')])->whereBetween('date', [$start, $end])->groupBy(['team', 'name'])->get();
        //dump($data->toArray());
        foreach ($data as $row){
            Reporting::write($row->team, $row->name, $category, $row->count);
        }
    }

    public static function countErrorsByMonth(\DateTime $date, string $category): void
    {
        $start = $date->modify('first day of this month')->format('Y-m-d H:i:s');
        $end = $date->modify('last day of this month')->format('Y-m-d').' 23:59:59';
        $data = Error::query()->select(['name', 'team', DB::raw('COUNT(guid) as count')])->whereBetween('date', [$start, $end])->groupBy(['team', 'name'])->get();
        foreach ($data as $row){
            Reporting::write($row->team, $row->name, $category, $row->count);
        }
    }

    public static function countErrorsByWeek(\DateTime $date, string $category): void
    {
        $start = $date->modify('Monday this week')->format('Y-m-d H:i:s');
        $end = $date->modify('Sunday this week')->format('Y-m-d').' 23:59:59';
        $data = Error::query()->select(['name', 'team', DB::raw('COUNT(guid) as count')])->whereBetween('date', [$start, $end])->groupBy(['team', 'name'])->get();
        foreach ($data as $row){
            Reporting::write($row->team, $row->name, $category, $row->count);
        }
    }

    public static function getTodayErrors($team): mixed
    {
        $data = Reporting::getByTeam($team, 'errors_today');
        $data = $data->sortByDesc('value')->take(5);
        return $data;
    }

}

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

    public static function getTop5TodayErrors($team): mixed
    {
        $data = Reporting::getByTeam($team, 'errors_today');
        $data = $data->sortByDesc('value')->take(5);
        return $data;
    }

    public static function get5daysErrors($team): array
    {
        $all_keys = collect([]);
        $days = collect([]);

        $data = Reporting::getByTeam($team, 'errors_today');
        $day0 = $data->sortBy('name')->pluck('value', 'name');
        $days->put(0, $day0);

        $data = Reporting::getByTeam($team, 'errors_yesterday');
        $day1 = $data->sortBy('name')->pluck('value', 'name');
        $days->put(1, $day1);

        $data = Reporting::getByTeam($team, 'errors_2days');
        $day2 = $data->sortBy('name')->pluck('value', 'name');
        $days->put(2, $day2);

        $data = Reporting::getByTeam($team, 'errors_3days');
        $day3 = $data->sortBy('name')->pluck('value', 'name');
        $days->put(3, $day3);

        $data = Reporting::getByTeam($team, 'errors_4days');
        $day4 = $data->sortBy('name')->pluck('value', 'name');
        $days->put(4, $day4);

        $all_keys = $all_keys->merge($day0->keys());
        $all_keys = $all_keys->merge($day1->keys());
        $all_keys = $all_keys->merge($day2->keys());
        $all_keys = $all_keys->merge($day3->keys());
        $all_keys = $all_keys->merge($day4->keys());
        $all_keys = $all_keys->unique()->sort();
        //dump($all_keys);

        $list = collect([]);
        foreach ($days->reverse() as $number => $day){
            $values = collect([]);
            foreach ($all_keys as $key){
                $values[] = $day->get($key, 0);
            }
            $name = (new \DateTime())->modify('-'.$number.' days')->format('d-m-Y');
            $list->push(['name'=>$name, 'data'=>$values->toArray()]);
        }

        return [
            'series'=>$list->values(),
            'categories'=>$all_keys->values()
        ];
    }

}

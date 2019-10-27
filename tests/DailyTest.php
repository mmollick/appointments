<?php declare(strict_types=1);

namespace MMollick\Appointments\Tests;

use Carbon\Carbon;
use MMollick\Appointments\Daily;
use MMollick\NaturalScheduler\DailyExpression;
use PHPUnit\Framework\TestCase;

class DailyTest extends TestCase
{
    /**
     * @param $startFrom
     * @param $timezone
     * @param $interval
     * @param $currentDate
     * @param $expectedIsDue
     * @param $expectedFirstRun
     * @param $expectedLastRun
     * @param $expectedDates
     * @dataProvider expressionProvider
     */
    public function test_public_methods($startFrom, $timezone, $interval, $currentDate, $expectedIsDue, $expectedFirstRun, $expectedLastRun, $expectedDates)
    {
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s e', $currentDate)->timezone(null));
        $model = (new Daily($interval))
            ->startSinceFormat('Y-m-d H:i:s', $startFrom)
            ->timezone($timezone);

        $this->assertEquals(
            $expectedIsDue,
            $model->isDueNow(),
            sprintf('Failed asserting that schedule is %s', $expectedIsDue ? 'due' : 'not due')
        );
        $this->assertEquals(
            $expectedFirstRun,
            $actual = $model->firstAppointment()->timezone(null)->format('Y-m-d H:i:s'),
            sprintf('Failed asserting that first run is %s, received %s', $expectedFirstRun, $actual)
        );
        $this->assertEquals(
            $expectedLastRun,
            $actual = $model->lastAppointment()->timezone(null)->format('Y-m-d H:i:s'),
            sprintf('Failed asserting that last run is %s, received %s', $expectedLastRun, $actual)
        );
        $expectedDates = (array) $expectedDates;
        for ($i = 0; $i < count($expectedDates); $i++) {
            $this->assertEquals(
                $expectedDates[$i],
                $actual = $model->nextAppointment($i)->timezone(null)->format('Y-m-d H:i:s'),
                sprintf('Failed asserting that next run after %s time(s) is %s, received %s', $i, $expectedDates[$i], $actual)
            );
        }
    }

    /**
     * @return array
     */
    public function expressionProvider() : array
    {
        return [
            // starting on 2019-01-01, run every 1 day(s) at 12:00 America/New_york
            'every_day' => ['2019-01-01 12:00:00','America/New_York', 1, '2019-01-01 12:00:00 America/New_York', true, '2019-01-01 17:00:00', '2019-01-01 17:00:00', ['2019-01-02 17:00:00', '2019-01-03 17:00:00']],
            // starting on 2019-01-01, run every other day(s) at 12:00 America/New_york
            'every_other_day' => ['2019-01-01 12:00:00','America/New_York', 2, '2019-01-01 12:00:00 America/New_York', true, '2019-01-01 17:00:00', '2019-01-01 17:00:00',['2019-01-03 17:00:00', '2019-01-05 17:00:00']],
            // starting on 2019-01-01, run every 3 day(s) at 12:00 America/New_york
            'every_three_days' => ['2019-01-01 12:00:00','America/New_York', 3, '2019-01-01 12:00:00 America/New_York', true, '2019-01-01 17:00:00', '2019-01-01 17:00:00',['2019-01-04 17:00:00', '2019-01-07 17:00:00']],
            // starting on 2019-01-31, run every 7 day(s) at 12:00 America/New_york
            'every_seven_days' => ['2019-01-01 12:00:00','America/New_York', 7, '2019-01-31 12:00:00 America/New_York', false,'2019-01-01 17:00:00', '2019-01-29 17:00:00', ['2019-02-05 17:00:00', '2019-02-12 17:00:00']],
            // starting on 2019-01-01, run every 365 day(s) at 12:00 America/New_york, 2020 is a leap year, so there will be 2 runs that year
            'every_365_days' => ['2019-01-01 12:00:00','America/New_York', 365, '2019-01-01 12:00:00 America/New_York', true,'2019-01-01 17:00:00', '2019-01-01 17:00:00', ['2020-01-01 17:00:00', '2020-12-31 17:00:00',  '2021-12-31 17:00:00']],

            // Ensure nextRun returns current day @ start_time if run same day before start_time; e.g. at 10:00 the next run time is today at 12:00
            'same_day_next_run' => ['2018-12-01 12:00:00','America/New_York', 1, '2019-01-01 10:00:00 America/New_York', false, '2018-12-01 17:00:00', '2018-12-31 17:00:00',  ['2019-01-01 17:00:00', '2019-01-02 17:00:00']],
            'same_day_next_run_minutes_before' => ['2018-12-01 12:00:00','America/New_York', 1, '2019-01-01 11:59:00 America/New_York', false, '2018-12-01 17:00:00', '2018-12-31 17:00:00', ['2019-01-01 17:00:00', '2019-01-02 17:00:00']],
            'same_day_next_run_minutes_after' => ['2018-12-01 12:00:00','America/New_York', 1, '2019-01-01 12:01:00 America/New_York', false, '2018-12-01 17:00:00', '2019-01-01 17:00:00', ['2019-01-02 17:00:00', '2019-01-03 17:00:00']],

            // starting on 2018-12-01, run every 1 day(s) at 12:00 America/New_york
            'year_overflow_every_day' => ['2018-12-01 12:00:00','America/New_York', 1, '2019-01-01 12:00:00 America/New_York', true, '2018-12-01 17:00:00', '2019-01-01 17:00:00', ['2019-01-02 17:00:00', '2019-01-03 17:00:00']],
            // starting on 2018-12-01, run every other day(s) at 12:00 America/New_york
            'year_overflow_every_other_day' => ['2018-12-01 12:00:00','America/New_York', 2, '2019-01-01 12:00:00 America/New_York', false, '2018-12-01 17:00:00', '2018-12-31 17:00:00', ['2019-01-02 17:00:00', '2019-01-04 17:00:00']],
            // starting on 2018-12-01, run every 3 day(s) at 12:00 America/New_york
            'year_overflow_every_three_days' => ['2018-12-01 12:00:00','America/New_York', 3, '2019-01-01 12:00:00 America/New_York', false, '2018-12-01 17:00:00', '2018-12-31 17:00:00', ['2019-01-03 17:00:00', '2019-01-06 17:00:00']],

            'start_in_future' => ['2019-01-31 12:00:00','America/New_York', 1, '2019-01-01 12:00:00 America/New_York', false, '2019-01-31 17:00:00', '2019-01-31 17:00:00', ['2019-01-31 17:00:00', '2019-02-01 17:00:00']],
        ];
    }
}

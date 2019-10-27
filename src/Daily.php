<?php declare(strict_types=1);

namespace MMollick\Appointments;

use Carbon\Carbon;

final class Daily extends ExpressionAbstract
{
    /**
     * @var \Carbon\Carbon
     */
    protected $startFrom;
    /**
     * @var \DateTimeZone
     */
    protected $timezone;
    /**
     * @var int
     */
    protected $interval;

    protected $pattern = ['startFrom', 'timezone', 'interval'];

    public function __construct(int $interval)
    {
        $this->interval = $this->parseInterval($interval);
        $this->startFrom = Carbon::now();
        $this->timezone = new \DateTimeZone('UTC');
        return $this;
    }

    public function nextAppointment(int $iterations = 0): Carbon
    {
        $lastRun = $this->lastAppointment();
        // If last run is in future, then this has never been run before, we'll
        // artificially create the next run by subtracting the interval
        if ($lastRun->isFuture()) {
            $lastRun->subDays($this->interval);
        }

        return $lastRun->addDays($this->interval * ($iterations + 1));
    }

    public function isDueNow(int $withinSeconds = 0): bool
    {
        return Carbon::now()->diffInSeconds($this->nextAppointment()) <= $withinSeconds
            || Carbon::now()->diffInSeconds($this->lastAppointment()) <= $withinSeconds;
    }

    public function lastAppointment(int $iterations = 0): Carbon
    {
        $firstAppointment = $this->firstAppointment();

        // Hasn't been run before, return first run date
        if ($firstAppointment->gt(Carbon::now())) {
            return $firstAppointment;
        }

        $diffFromNow = $firstAppointment->diffInDays(Carbon::now());
        $pastRuns = floor($diffFromNow / $this->interval);
        return $firstAppointment->copy()->addDays($this->interval * $pastRuns);
    }

    public function firstAppointment(): Carbon
    {
        return $this->startFrom->copy()->shiftTimezone($this->timezone);
    }

    public function buildExpression(): string
    {
        // TODO: Implement buildExpression() method.
    }

    public function toArray(): array
    {
        return [
            'startFrom' => $this->startFrom,
            'timezone' => $this->timezone,
            'interval' => $this->interval,
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}

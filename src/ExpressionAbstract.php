<?php declare(strict_types=1);

namespace MMollick\Appointments;

use Carbon\Carbon;
use MMollick\NaturalScheduler\Util;

abstract class ExpressionAbstract implements ExpressionContract, \JsonSerializable
{
    public function startSinceDateTime(\DateTimeInterface $dateTime): ExpressionContract
    {
        $this->startFrom = $dateTime;
        return $this;
    }

    public function startSinceFormat(string $format, string $string): ExpressionContract
    {
        $dateTime = Carbon::createFromFormat($format, $string);
        if (!$dateTime) {
            throw new \InvalidArgumentException('An invalid start since was specified.');
        }

        $this->startSinceDateTime($dateTime);
        return $this;
    }

    public function timezone($timezone): ExpressionContract
    {
        $this->timezone = $this->parseTimeZone($timezone);
        return $this;
    }

    protected function parseInterval($interval) : int
    {
        if (!is_numeric($interval) || $interval < 1) {
            throw new \InvalidArgumentException('An invalid interval was specified; intervals must be greater than 0. Received: ' . $interval);
        }
        return (int) $interval;
    }

    protected function parseTimeZone($input) : \DateTimeZone
    {
        if ($input instanceof \DateTimeZone) {
            return $input;
        }

        try {
            return new \DateTimeZone($input);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('An invalid time zone was specified.');
        }
    }
}
<?php

namespace MMollick\Appointments;

use Carbon\Carbon;

interface ExpressionContract
{
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const FIRST_WEEK = 1;
    const SECOND_WEEK = 2;
    const THIRD_WEEK = 3;
    const FOURTH_WEEK = 4;
    const FOURTH_TO_LAST_WEEK = -4;
    const THIRD_TO_LAST_WEEK = -3;
    const SECOND_TO_LAST_WEEK = -2;
    const LAST_WEEK = -1;

    public function startSinceDateTime(\DateTimeInterface $dateTime) : self;
    public function startSinceFormat(string $format, string $string) : self;
    public function timezone($timezone) : self;
    public function buildExpression() : string;
    public function toArray() : array;
    public function nextAppointment(int $iterations = 0) : Carbon;
    public function isDueNow(int $withinSeconds = 0) : bool;
    public function lastAppointment(int $iterations = 0) : Carbon;
    public function firstAppointment() : Carbon;
}

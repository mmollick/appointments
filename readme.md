Appointments
===

Appointments is a _natural scheduler_ library written in PHP.

## Features

* Specify start date and time for recurring appointment calculation
* Timezone support for time calculations
* Simple and complex interval calculations available
* Full week of month & day of month control
* Expandable

## Installation

> Not yet available, coming soon

## What is Natural Scheduling?

Cron expressions have been the standard for scheduling tasks on computers since the [1970s](https://en.wikipedia.org/wiki/Cron). When it comes to scheduling repetitive tasks like daily cleanups and monthly archiving it's great. However, when you start getting into more specific, natural scheduling, like running a task [every two weeks](https://serverfault.com/questions/633264/cronjob-run-every-two-weeks-on-saturday-starting-on-this-saturday/633272) or [every first monday of each month](https://stackoverflow.com/questions/3241086/how-to-schedule-to-run-first-sunday-of-every-month) cron expressions end up looking more like bash scripts.

It's cases like this where Natural Scheduling excels. We start by specifying a date and time from which we calculate future appointments. This allows us create complex and specific expressions that otherwise wouldn't be possible with traditional Cron expressions.

### Examples

```
// Starting at 2019-01-01 12:30:10 New York time run every 90 seconds
$seconds = Seconds::createSinceFormat('2019-01-01 12:30:10', 'Y-m-d H:i:s', 90, 'America/New_York');
$seconds->nextRun(); // 2019-01-01 12:31:40 America/New_York

// Starting at 2019-01-01 12:00:00 New York time run every other month on the 2nd Tuesday
$monthly = MonthlyWithWeek::createSinceFormat('2019-01-01 12:00:00', 'Y-m-d H:i:s', 2, 2, 2, 'America/New_York');
$monthly->nextRun(); // 2019-03-11 12:00:00
```

## API

### Instantiating Expressions

The expressions below represent the type of scheduling this library currently supports and how they are instantiated. 

#### Seconds

Expression: `start_iso_8061|timezone|interval`

```
// Static Methods
$seconds = Seconds::createSinceNow($interval);
$seconds = Seconds::createFromExpression($expression);
$seconds = Seconds::createSinceDateTime($dateTime, $interval, $timezone);
$seconds = Seconds::createSinceFormat($format, $date, $interval, $timezone);

// Class
$seconds = new Seconds($interval);
$seconds->startSinceDateTime(DateTime);
$seconds->startSinceFormat($format, $date);
$seconds->timezone($timezone);
```

#### Minutes

Expression: `start_iso_8061|timezone|interval`

```
// Static Methods
$minutes Minutes::createSinceNow($interval);
$minutes Minutes::createFromExpression($expression);
$minutes Minutes::createSinceDateTime($dateTime, $interval, $timezone);
$minutes Minutes::createSinceFormat($format, $date, $interval, $timezone);

// Class
$minutes new Minutes($interval);
$minutes->startSinceDateTime(DateTime);
$minutes->startSinceFormat($format, $date);
$minutes->timezone($timezone);
```

#### Hourly

Expression: `start_iso_8061|timezone|interval`

```
// Static Methods
$hourly = Hourly::createSinceNow($interval);
$hourly = Hourly::createFromExpression($expression);
$hourly = Hourly::createSinceDateTime($dateTime, $interval, $timezone);
$hourly = Hourly::createSinceFormat($format, $date, $interval, $timezone);

// Class
$hourly = new Hourly($interval);
$hourly->startSinceDateTime(DateTime);
$hourly->startSinceFormat($format, $date);
$hourly->timezone($timezone);
```

#### Daily

Expression: `start_iso_8061|timezone|interval`

```
// Static Methods
$daily = Daily::createSinceNow($interval);
$daily = Daily::createFromExpression($expression)
$daily = Daily::createSinceDateTime($dateTime, $interval, $timezone);
$daily = Daily::createSinceFormat($format, $date, $interval, $timezone);

// Class
$daily = new Daily($interval);
$daily->startSinceDateTime(DateTime);
$daily->startSinceFormat($format, $date);
$daily->timezone($timezone);
```

#### Weekly

Expression: `start_iso_8061|timezone|interval|day_of_week`

```
// Static Methods
$weekly = Weekly::createSinceNow($interval, $dow);
$weekly = Weekly::createFromExpression($expression);
$weekly = Weekly::createSinceDateTime($dateTime, $interval, $dow, $timezone);
$weekly = Weekly::createSinceFormat($format, $date, $interval, $dow, $timezone);

// Class
$weekly = new Weekly($interval, $dow);
$weekly->startSinceDateTime(DateTime);
$weekly->startSinceFormat($format, $date);
$weekly->timezone($timezone);
```

#### Monthly

Expression: `start_iso_8061|timezone|interval|day_of_month`

```
// Static Methods
$monthly = Monthly::createSinceNow($interval, $dom);
$monthly = Monthly::createFromExpression($expression);
$monthly = Monthly::createSinceDateTime($dateTime, $interval, $dom, $timezone);
$monthly = Monthly::createSinceFormat($format, $date, $interval, $dom, $timezone);

// Class
$monthly = new Monthly($interval, $dom);
$monthly->startSinceDateTime(DateTime);
$monthly->startSinceFormat($format, $date);
$monthly->timezone($timezone);
```

#### Monthly w/ Week

Expression: `start_iso_8061|timezone|interval|week|day_of_week`

```
// Static Methods
$monthly = MonthlyWithWeek::createSinceNow($interval, $week, $dow);
$monthly = MonthlyWithWeek::createFromExpression($expression);
$monthly = MonthlyWithWeek::createSinceDateTime($dateTime, $interval, $week, $dow, $timezone);
$monthly = MonthlyWithWeek::createSinceFormat($format, $date, $interval, $week, $dow, $timezone);

// Class
$monthly = new MonthlyWithWeek($interval, $week, $dow);
$monthly->startSinceDateTime(DateTime);
$monthly->startSinceFormat($format, $date);
$monthly->timezone($timezone);
```

### Working with Expressions

#### Determine the Next Appointment

The `nextRun` method allows us to calculate the next `DateTime` this appointment falls on. We can optionally specify the `$iterations` argument to specify the number of Appointments in the future we should calculate.

```
$expression = Daily::createSinceNow($interval);
$expression->nextAppointment($interations = 0);
```

#### Is the Appointment Now?

The `isDueNow` method will determine if the Appointment is happening now. We also specify a `$withinSeconds` argument to allow a window of +/- seconds of specificity.

```
$expression = Daily::createSinceNow($interval);
$expression->isDueNow($withinSeconds = 0);
```

#### Is the Appointment on a Specific Date or Time?

The `isDueAt` and `isDueAtFormat` method will determine if the Appointment is happening at a specific date or time.

```
$expression = Daily::createSinceNow($interval);
$expression->isDueAtDateTime($dateTime);
$expression->isDueAtFormat($string, $format);
```

#### When was the Last Appointment

The `lastRun` method calculates the last Appointment. We can optionally specify the `$iterations` argument to specify how many Appointments in the past we want to calculate.

```
$expression = Daily::createSinceNow($interval);
$expression->lastAppointment($iterations = 0);
```

#### When was the First Appointment?

The `firstRun` method will return the very first Appointment.

```
$expression = Daily::createSinceNow($interval);
$expression->firstRun();
```

#### Get Expression as a String

The `buildExpression` method will return the expression string you can use to rebuild this expression.

```
$expression = Daily::createSinceNow($interval);
$expression->buildExpression();
```

#### Get Expression as an Array

The `toArray` method will return the expression as an array.

```
$expression = Daily::createSinceNow($interval);
$expression->toArray();
```
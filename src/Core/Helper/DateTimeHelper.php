<?php

namespace Core\Helper;

class DateTimeHelper extends \DateTime
{
    const EU = 'eu';
    const RU = 'ru';

    const EU_DATE_FORMAT = 'd/m/Y H:i';
    const RU_DATE_FORMAT = 'Y-m-d H:i:s';

    const FORMATS = [
        self::EU => self::EU_DATE_FORMAT,
        self::RU => self::RU_DATE_FORMAT
    ];

    public function format($format = self::EU)
    {
        return parent::format(self::FORMATS[$format]);
    }

    public static function createFromFormat($format, $time, $timezone = null)
    {
        $dateTime = parent::createFromFormat($format, $time, $timezone);

        if (false === $dateTime) {
            $dateTime = date_create($time);
        }

        return false === $dateTime ?: new self($dateTime->format(self::RU_DATE_FORMAT));
    }
}
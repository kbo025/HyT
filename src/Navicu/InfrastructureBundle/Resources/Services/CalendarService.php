<?php
namespace Navicu\InfrastructureBundle\Resources\Services;

/**
 * Servicio encargado de generar calendarios y funcionalidades relacionadas con fechas.
 *
 * @package Castor\PresentationBundle\Services
 */
class CalendarService
{
    protected $container;
    protected $translator;

    // Se toma como calendario base para la vista uno con 7 días y
    // 6 semanas, 42 celdas en total.
    const CELLS_PER_MONTH = 42;

    public function __construct($container, $translator)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Genera un calendario con 24 meses a partir de la fecha actual.
     *
     * Cuando se necesita crear un calendario para luego desplegarlos en la
     *
     * @return Array Calendario de 24 meses.
     *
     * @author Arturo Rossodivita
     */
    public function getCalendars()
    {
        $today = getdate();
        $month = $today['mon'];
        $year = $today['year'];
        $week = 1;

        for ($i = 0; $i < 24; $i++) {
            $lastMonthDay = date("d", (mktime(0, 0, 0, $month + 1, 1, $today['year']) - 1));
            $monthName = $this->getMonthName($month);

            for ($k = 1; $k <= $lastMonthDay; $k++) {
                $weekDay = date('N', strtotime($year . '-' . $month . '-' . $k));

                $calendars[$monthName . " " . $year]['monthNumber'] = $month;
                $calendars[$monthName . " " . $year]['yearNumber'] = $year;
                $calendars[$monthName . " " . $year][$week][$weekDay] = $k;
                if ($weekDay == 7)
                    $week++;
            }

            if ($month < 12) {
                $month++;
                $week = 1;
            } else {
                $month = 1;
                $week = 1;
                $year++;
            }
        }

        return $calendars;
    }

    public function getCalendarWithMarkedDays($dates_data)
    {
        $marked_dates = new \SplQueue();
        $dd = new \ArrayObject($dates_data);

        // Almacenar fechas en una cola para manipularlas mejor.
        for ($iter = $dd->getIterator(); $iter->valid(); $iter->next()) {
            $marked_dates->enqueue($iter->current());
        }

        $start_date = new \DateTime($marked_dates->bottom());
        $end_date = new \DateTime($marked_dates->top());

        // Meses de diferencia entre fecha inicial y fecha final
        $interval = (($end_date->format("Y") - $start_date->format("Y")) * 12) +
            ($end_date->format("m") - $start_date->format("m")) + 1;

        $calendar = new \SplFixedArray(self::CELLS_PER_MONTH * $interval);

        // Primer dia de del calendario
        $current_date = new \DateTime($start_date->format("Y-m-01"));
        $base_index = $month_index = 0;
        $index = ((int) $current_date->format("N")) - 1;

        while ($current_date->format("Y-m-d") <= $end_date->format("Y-m-t")) {
            // Verifica si la fecha actual esta en el conjunto de fechas
            // marcadas en la carga masiva.
            if (!$marked_dates->isEmpty() &&
                $current_date->format("Y-m-d") == $marked_dates->bottom()) {
                $calendar->offsetSet($base_index + $index,
                    $current_date->format("+Y-m-d"));
                $marked_dates->dequeue();
            } else {
                $calendar->offsetSet($base_index + $index,
                    $current_date->format("Y-m-d"));
            }

            // Se verifica si se alcanzo el fin de mes
            if ($current_date->format("d") == $current_date->format("t")) {
                $index = 0;
                $month_index++;
                $foo = clone $current_date;
                $foo->add(new \DateInterval("P1D"));
                $week_day = ((int) $foo->format("N")) - 1;
                $base_index = self::CELLS_PER_MONTH * $month_index + $week_day;
            } else {
                $index++;
            }

            $current_date->add(new \DateInterval("P1D")); // Sumar 1 dia
        }

        return array(
            "start_date" => $start_date->format("Y-m-d"),
            "end_date" => $end_date->format("Y-m-d"),
            "calendar" => $calendar->toArray()
        );
    }

    /**
     * Se obtiene la traducción del nombre del mes por número.
     *
     * @param $month Número del mes. Enero es 1, diciembre es 12.
     *
     * @return string Traducción del nombre del mes.
     *
     * @author Arturo Rossodivita
     */
    public function getMonthName($month)
    {
        switch ($month) {
            case 1:
                $month = $this->translator->trans('share.calendar.months.jan');
                break;
            case 2:
                $month = $this->translator->trans('share.calendar.months.feb');
                break;
            case 3:
                $month = $this->translator->trans('share.calendar.months.mar');
                break;
            case 4:
                $month = $this->translator->trans('share.calendar.months.apr');
                break;
            case 5:
                $month = $this->translator->trans('share.calendar.months.may');
                break;
            case 6:
                $month = $this->translator->trans('share.calendar.months.jun');
                break;
            case 7:
                $month = $this->translator->trans('share.calendar.months.jul');
                break;
            case 8:
                $month = $this->translator->trans('share.calendar.months.aug');
                break;
            case 9:
                $month = $this->translator->trans('share.calendar.months.sep');
                break;
            case 10:
                $month = $this->translator->trans('share.calendar.months.oct');
                break;
            case 11:
                $month = $this->translator->trans('share.calendar.months.nov');
                break;
            case 12:
                $month = $this->translator->trans('share.calendar.months.dec');
                break;
        }

        return $month;
    }
}

/* End of file */
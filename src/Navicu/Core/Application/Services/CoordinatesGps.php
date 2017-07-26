<?php
namespace Navicu\Core\Application\Services;

/**
 * Clase contiene la función getGps que calcula las coordenadas Gps
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 12/11/2015
 */
class CoordinatesGps {

    /**
     * La siguiente función calcula las coordenadas Gps de un punto dado la latitud y longitud
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 11/10/2015
     * @param $latitude
     * @param $longitude
     * @return null|string
     */
    public static function getGps($latitude, $longitude)
    {
        $gps = null;
        $degress = abs(intval($latitude));
        $minutes = abs(intval(60 * (abs($latitude) - $degress)));
        $seconds = round(3600 * ((abs($latitude) - $degress) - $minutes/60), 4);

        $gps = $degress.'°'.$minutes.'\''.$seconds.'\'\''.'N';

        $degress = abs(intval($longitude));
        $minutes = abs(intval(60 * (abs($longitude) - $degress)));
        $seconds = round(3600 * ((abs($longitude) - $degress) - $minutes/60), 4);

        $gps = $gps.' '.$degress.'°'.$minutes.'\''.$seconds.'\'\''.'W';

        return $gps;
    }
}
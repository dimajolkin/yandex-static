<?php

namespace YandexStatic\strategy;


use PHPCoord\LatLng;
use phpdk\awt\Point;

class Marcutor
{
    /** @var  int */
    protected $zoom;

    /** @var  Point */
    private $mapSize;

    protected static $cof = [
        0 => [0, 0],
        1 => [-128, -128],
        2 => [-256, -256],
    ];

    /**
     * Marcutor constructor.
     * @param int $zoom
     * @param Point $mapSize
     */
    public function __construct($zoom, Point $mapSize)
    {
        $this->zoom = $zoom;
        $this->mapSize = $mapSize;
    }

    protected function dx(Point $point): Point
    {
        return new Point(
            $point->getX() + static::$cof[$this->zoom][0],
            $point->getY() + static::$cof[$this->zoom][1]
        );
    }

    public function convertInMapPixelCoords(LatLng $map): Point
    {
        $map_width = 256 * ($this->zoom + 1);
        $map_height = 256 * ($this->zoom + 1);

        $Y = log(tan((($map->getLat() * M_PI / 180) / 2) + M_PI_4)) ;
        $X = $map->getLng() + 180;

        $X = (($map_width * $X) / 360);
        $Y = ($map_height / 2.) - (($map_width * $Y) / (2. * M_PI));

        return $this->dx(new Point($X, $Y));
    }
}
<?php

namespace YandexStatic;

use PHPCoord\LatLng;

class Label
{
    const STYLE_PM = 'pm';
    const STYLE_PM2 = 'pm2';

    const SIZE_SMALL = 's';
    const SIZE_MIDDLE = 'm';
    const SIZE_LONG = 'l';

    const COLOR_RED = 'rd';


    /** @var  LatLng */
    protected $point;

    /** @var  string */
    protected $style;

    /** @var  string */
    protected $color;


    /** @var  string */
    protected $size;

    /** @var  int */
    protected $content = 0;


    /**
     * Label constructor.
     * @param LatLng $point
     * @param string $style
     * @param string $color
     * @param string $size
     * @param int $content
     */
    public function __construct(
        LatLng $point,
        $style = self::STYLE_PM2,
        $color = self::COLOR_RED,
        $size = self::SIZE_MIDDLE,
        $content = null
    )
    {
        $this->point = $point;
        $this->style = $style;
        $this->color = $color;
        $this->size = $size;
        $this->content = $content;
    }


    /**
     * @return LatLng
     */
    public function getLatLng(): LatLng
    {
        return $this->point;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getContent(): ?int
    {
        return $this->content;
    }
}
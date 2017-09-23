<?php

namespace YandexStatic;

use Types\interfaces\ImageInterface;
use Types\postGIS\Polygon;
use Types\TString;

class ImagePolygon implements ImageInterface
{
    /** @var  Polygon */
    protected $polygon;

    protected $size = [300, 300];

    /**
     * ImagePolygon constructor.
     *
     * @param Polygon $polygon
     */
    public function __construct(Polygon $polygon)
    {
        $this->polygon = $polygon;
    }

    /**
     * @param array [x, y] $size
     *
     * @return \Types\yandex\Image
     */
    public function setSize(array $size)
    {

        if ($size[0] > 600) {
            $size[0] = 650;
        }

        if (empty($size[1])) {
            $size[1] = 450;
        }

        $this->size = $size;
        return $this;
    }

    public function getPath(): string
    {
        return '';
    }

    public function toString(): TString
    {
        return TString::new("https://static-maps.yandex.ru/1.x/?l=map&size={size}&pl=c:df362a,f:df362a33,w:2,{cords}")->format([
            'cords' => implode(',', array_map(function ($data) {
                return $data[1] . ',' . $data[0];
            }, $this->polygon->toArray()[0])),
            'size' => $this->size[0] . ',' . $this->size[1],
        ]);
    }

    public function __toString()
    {
        return $this->toString()->toString();
    }


}
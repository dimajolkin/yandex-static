<?php

namespace YandexStatic\strategy;

use Imagick;

class GenerateDynamicImageStrategy extends AbstractImageGenerateStrategy
{

    public static function cycleRestrict($value, $min, $max)
    {
        return $value - floor(($value - $min) / ($max - $min)) * ($max - $min);
    }


    public static function xToLongtitude($vectorX, $zoom)
    {

        $c_180pi = 180 / M_PI;

        return self::cycleRestrict(M_PI * $vectorX / pow(2, $zoom + 7) - M_PI, -M_PI, M_PI) * $c_180pi;
    }

    public static function yToLatitude($vectorY)
    {
        $e = 0.0818191908426;
        $e2 = $e * $e;
        $e4 = $e2 * $e2;
        $e6 = $e4 * $e2;
        $e8 = $e4 * $e4;
        $radius = 6378137;
        $subradius = 1 / $radius;
        $d2 = $e2 / 2 + 5 * $e4 / 24 + $e6 / 12 + 13 * $e8 / 360;
        $d4 = 7 * $e4 / 48 + 29 * $e6 / 240 + 811 * $e8 / 11520;
        $d6 = 7 * $e6 / 120 + 81 * $e8 / 1120;
        $d8 = 4279 * $e8 / 161280;
        $c_180pi = 180 / M_PI;
        $xphi = M_PI * 0.5 - 2 * atan(1 / exp($vectorY * $subradius));
        $geoY = $xphi + $d2 * sin(2 * $xphi) + $d4 * sin(4 * $xphi) + $d6 * sin(6 * $xphi) + $d8 * sin(8 * $xphi);
        $geoY = $geoY * $c_180pi;

        return $geoY;
    }


    public static function fromGlobalsPixels($vectorX, $vectorY, $zoom): Point
    {

        $coef = 1; //2 >> $zoom;
        $vectorX = $vectorX * $coef;
        $vectorY = $vectorY * $coef;
        $radius = 6378137;
        $equator = 2 * M_PI * $radius;
        $subequator = 1 / $equator;
        $halfEquator = $equator / 2;
        $c_180pi = 180 / M_PI;
        $pixelsPerMeter = pow(2, $zoom + 8) * $subequator;
        $vectorX = self::cycleRestrict(M_PI * $vectorX / pow(2, $zoom + 7) - M_PI, -M_PI, M_PI) * $c_180pi;
        $vectorY = self::yToLatitude($halfEquator - $vectorY / $pixelsPerMeter);

        return new Point($vectorX, $vectorY);
    }


    function lon2x($lon) { return deg2rad($lon) * 6378137.0; }
    function lat2y($lat) { return log(tan(M_PI_4 + deg2rad($lat) / 2.0)) * 6378137.0; }

    private function convertGlobalToPixel(Image $image, PointMap $map): Point
    {
        //        z = 2
//        $map_width = 256 * 3;
//        $map_height = 256 * 3;
//
//        $rlat = $map->getLat() * M_PI / 180;
//        $Y = log(tan(($rlat / 2) + (M_PI / 4)));
//
//        $Y = ($map_height / 2) - ($map_width * $Y / (2 * M_PI));
//        $X = ($map_width * (180 + $map->getLng()) / 360) % $map_width;
//
//        $X = floor($X);
//        $Y = floor($Y);
//
//        $X += -2 - 256;
//        $Y += -33 - 256;


//        die;
//        z = 1
//        $map_width = 256 * 2;
//        $map_height = 256 * 2;
//
//        $rlat = $map->getLat() * M_PI / 180;
//        $Y = log(tan(($rlat / 2) + (M_PI / 4)));
//
//        $Y = ($map_height / 2) - ($map_width * $Y / (2 * M_PI));
//        $X = ($map_width * (180 + $map->getLng()) / 360) % $map_width;
//
//        $X += -8 - 128;
//        $Y += -28 - 128;

        //        z = 1
        $m = new Marcutor($image->getZoom(), new Point(256, 256));
        $p =  $m->convertInMapPixelCoords($map);

        return $p;
    }

    private function createLabel(): Imagick
    {
        $label = new Imagick("label.png");
        $label->resizeImage(30, 30, Imagick::FILTER_COSINE, 1);

        return $label;
    }

    public function generateUrl(Image $image): string
    {
        $static = (new DefaultGenerateImageStaticStrategy())->generateUrl($image);

        $img = new Imagick($static);

        $templateLabel = $this->createLabel();
        foreach ($image->getListLabels() as $label) {

            $point = $this->convertGlobalToPixel($image, $label->getPoint());
            $img->compositeImage(
                $templateLabel,
                Imagick::COMPOSITE_ATOP,
                $point->getX() - 10,
                $point->getY() - 30
            );
        }


        $img->writeImage($src = '1.jpg');

        return $src;
    }
}
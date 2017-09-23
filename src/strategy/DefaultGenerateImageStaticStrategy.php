<?php

namespace YandexStatic\strategy;


use YandexStatic\Image;
use YandexStatic\Label;

class DefaultGenerateImageStaticStrategy extends AbstractImageGenerateStrategy
{
    const MAX_LABELS = 100;

    public function generateUrl(Image $image): string
    {
        $url = "https://static-maps.yandex.ru/1.x/?z={$image->getZoom()}&l=map&";

        if ($image->getLatLng()) {
            $p = $image->getLatLng();
            $url .= "ll={$p->getLng()},{$p->getLat()}&";
        }

        if ($image->getSize()) {
            $s = $image->getSize();
            $url .= "size={$s->getX()},{$s->getY()}&";
        }

        if ($image->getListLabels()) {

            return $url . 'pt=' . implode('~', array_map([$this, 'generateLabel'], array_slice($image->getListLabels(), 0, static::MAX_LABELS)));
        }

        return $url;
    }

    public function generateLabel(Label $label): string
    {
        $p = $label->getLatLng();

        $pt = "{$p->getLng()},{$p->getLat()}";
        $options = [];
        if ($label->getStyle()) {
            $options[] = $label->getStyle();
        }

        if ($label->getColor()) {
            $options[] = $label->getColor();
        }

        if ($label->getSize()) {
            $options[] = $label->getSize();
        }

        if ($label->getContent()) {
            $options[] = $label->getContent();
        }

        if ($options) {
            return $pt . ',' . implode($options);
        }

        return $pt;
    }
}
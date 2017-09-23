<?php

namespace YandexStatic\strategy;

use YandexStatic\Image;

abstract class AbstractImageGenerateStrategy
{
    abstract public function generateUrl(Image $image): string;
}
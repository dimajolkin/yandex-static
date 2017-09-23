<?php

namespace YandexStatic;

use PHPCoord\LatLng;
use phpdk\awt\Point;
use phpdk\io\image\Image as IOImage;
use YandexStatic\strategy\AbstractImageGenerateStrategy;
use YandexStatic\strategy\DefaultGenerateImageStaticStrategy;

class Image extends IOImage
{
    protected $point;
    protected $zoom;
    protected $size = [300, 300];

    protected $listLabel = [];

    /** @var string  */
    protected $defaultStrategyClass = DefaultGenerateImageStaticStrategy::class;

    /** @var  AbstractImageGenerateStrategy */
    protected $strategy;

    /**
     * Image constructor.
     *
     * @param LatLng $point
     * @param AbstractImageGenerateStrategy|null $generateStrategy
     */
    public function __construct(
        LatLng $point = null,
        AbstractImageGenerateStrategy $generateStrategy = null
    )
    {
        $this->point = $point;
        $this->strategy = $generateStrategy;
    }

    public function getStrategy(): AbstractImageGenerateStrategy
    {
        if (!$this->strategy) {
            $class = $this->defaultStrategyClass;
            return $this->strategy = new $class();
        }

        return $this->strategy;
    }

    /**
     * @param AbstractImageGenerateStrategy $strategy
     */
    public function setStrategy(AbstractImageGenerateStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return LatLng
     */
    public function getLatLng(): ?LatLng
    {
        return $this->point;
    }

    /**
     * @param mixed $zoom
     *
     * @return Image|static
     */
    public function setZoom(int $zoom): self
    {
        $this->zoom = $zoom;

        return $this;
    }


    public function addLabel(Label $label)
    {
        $this->listLabel[] = $label;
    }

    /**
     * @return Label[]
     */
    public function getListLabels(): array
    {
        return $this->listLabel;
    }

    /**
     * @param array [x, y] $size
     *
     * @return static
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

    public function getZoom(): ?int
    {
        return $this->zoom;
    }

    /**
     * @return Point
     */
    public function getSize(): Point
    {
        return new Point($this->size[0], $this->size[1]);
    }


    public function __toString()
    {
        try {
            return (string)$this->toString();
        } catch (\Exception $ex) {
            return '';
        }
    }

    public function getUrl(): string
    {
        return $this->getStrategy()->generateUrl($this);
    }

    public function getPath(): string
    {
        return '';
    }
}
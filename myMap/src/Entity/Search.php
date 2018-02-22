<?php

namespace App\Entity;


class Search
{
    /* Fields */
    protected $location;
    protected $markerType;
    protected $markerColor;

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getMarkerType()
    {
        return $this->markerType;
    }

    public function setMarkerType($markerType)
    {
        $this->markerType = $markerType;
    }

    public function getMarkerColor()
    {
        return $this->markerColor;
    }

    public function setMarkerColor($markerColor)
    {
        $this->markerColor = $markerColor;
    }
}
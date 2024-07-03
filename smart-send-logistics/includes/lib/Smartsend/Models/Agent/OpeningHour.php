<?php

namespace Smartsend\Models\Agent;

class OpeningHour implements \JsonSerializable
{
    private $day;
    private $opens;
    private $closes;

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     * @return OpeningHour
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param mixed $opens
     * @return OpeningHour
     */
    public function setOpens($opens)
    {
        $this->opens = $opens;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCloses()
    {
        return $this->closes;
    }

    /**
     * @param mixed $closes
     * @return OpeningHour
     */
    public function setCloses($closes)
    {
        $this->closes = $closes;
        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }
}
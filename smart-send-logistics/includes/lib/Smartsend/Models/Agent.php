<?php

namespace Smartsend\Models;

use Smartsend\Models\Agent\Coordinates;
use Smartsend\Models\Agent\OpeningHour;

require_once 'Agent/OpeningHour.php';
require_once 'Agent/Coordinates.php';

class Agent implements \JsonSerializable
{
    //private $id; Only returned by API
    //private $type='agent'; Only returned by API
    private $agent_no;
    private $carrier;
    private $company;
    private $name_line1;
    private $name_line2;
    private $address_line1;
    private $address_line2;
    private $postal_code;
    private $city;
    private $country;
    private $coordinates;
    private $opening_hours;

    /**
     * @return mixed
     */
    public function getAgentNo()
    {
        return $this->agent_no;
    }

    /**
     * @param mixed $agent_no
     * @return Agent
     */
    public function setAgentNo($agent_no)
    {
        $this->agent_no = $agent_no;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param mixed $carrier
     * @return Agent
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     * @return Agent
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameLine1()
    {
        return $this->name_line1;
    }

    /**
     * @param mixed $name_line1
     * @return Agent
     */
    public function setNameLine1($name_line1)
    {
        $this->name_line1 = $name_line1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameLine2()
    {
        return $this->name_line2;
    }

    /**
     * @param mixed $name_line2
     * @return Agent
     */
    public function setNameLine2($name_line2)
    {
        $this->name_line2 = $name_line2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressLine1()
    {
        return $this->address_line1;
    }

    /**
     * @param mixed $address_line1
     * @return Agent
     */
    public function setAddressLine1($address_line1)
    {
        $this->address_line1 = $address_line1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddressLine2()
    {
        return $this->address_line2;
    }

    /**
     * @param mixed $address_line2
     * @return Agent
     */
    public function setAddressLine2($address_line2)
    {
        $this->address_line2 = $address_line2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @param mixed $postal_code
     * @return Agent
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Agent
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return Agent
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param Coordinates $coordinates
     * @return Agent
     */
    public function setCoordinates(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpeningHours()
    {
        return $this->opening_hours;
    }

    /**
     * @param mixed $opening_hours
     * @return Agent
     */
    public function setOpeningHours($opening_hours)
    {
        $this->opening_hours = $opening_hours;
        return $this;
    }

    /**
     * @param OpeningHour $opening_hour
     * @return Agent
     */
    public function addOpeningHours(OpeningHour $opening_hour)
    {
        if (is_array($this->opening_hours)) {
            $this->opening_hours[] = $opening_hour;
        } else {
            $this->setOpeningHours(array($opening_hour));
        }

        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

}
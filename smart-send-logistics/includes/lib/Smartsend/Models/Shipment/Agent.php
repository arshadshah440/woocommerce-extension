<?php

namespace Smartsend\Models\Shipment;

class Agent implements \JsonSerializable
{
    private $internal_id;
    private $internal_reference;
    private $agent_no;
    private $company;
    private $name_line1;
    private $name_line2;
    private $address_line1;
    private $address_line2;
    private $postal_code;
    private $city;
    private $country;
    private $sms;
    private $email;

    public function __construct($receiver=array())
    {
        if (isset($receiver['internal_id'])) {
            $this->setInternalId($receiver['internal_id']);
        }

        if (isset($receiver['internal_reference'])) {
            $this->setInternalReference($receiver['internal_reference']);
        }

        if (isset($receiver['agent_no'])) {
            $this->setAgentNo($receiver['agent_no']);
        }

        if (isset($receiver['company'])) {
            $this->setCompany($receiver['company']);
        }

        if (isset($receiver['name_line1'])) {
            $this->setNameLine1($receiver['name_line1']);
        }

        if (isset($receiver['name_line2'])) {
            $this->setNameLine2($receiver['name_line2']);
        }

        if (isset($receiver['address_line1'])) {
            $this->setAddressLine1($receiver['address_line1']);
        }

        if (isset($receiver['address_line2'])) {
            $this->setAddressLine2($receiver['address_line2']);
        }

        if (isset($receiver['postalCode'])) {
            $this->setPostalCode($receiver['postalCode']);
        }

        if (isset($receiver['city'])) {
            $this->setCity($receiver['city']);
        }

        if (isset($receiver['country'])) {
            $this->setCountry($receiver['country']);
        }

        if (isset($receiver['sms'])) {
            $this->setSms($receiver['sms']);
        }

        if (isset($receiver['email'])) {
            $this->setEmail($receiver['email']);
        }
    }

    /**
     * @return mixed
     */
    public function getInternalId()
    {
        return $this->internal_id;
    }

    /**
     * @param string $internal_id
     * @return Agent
     */
    public function setInternalId($internal_id)
    {
        $this->internal_id = (string) $internal_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternalReference()
    {
        return $this->internal_reference;
    }

    /**
     * @param string $internal_reference
     * @return Agent
     */
    public function setInternalReference($internal_reference)
    {
        $this->internal_reference = (string) $internal_reference;
        return $this;
    }

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
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * @param mixed $sms
     * @return Agent
     */
    public function setSms($sms)
    {
        $this->sms = $sms;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Agent
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }

}
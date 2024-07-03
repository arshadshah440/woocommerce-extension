<?php

namespace Smartsend\Models\Shipment;


class Item implements \JsonSerializable
{
    private $internal_id;
    private $internal_reference;
    private $sku;
    private $name;
    private $description;
    private $hs_code;
    private $country_of_origin;
    private $image_url;
    private $unit_weight;
    private $unit_price_excluding_tax;
    private $unit_price_including_tax;
    private $quantity;
    private $total_price_excluding_tax;
    private $total_price_including_tax;
    private $total_tax_amount;

    public function __construct($item=array())
    {
        if (isset($item['internal_id'])) {
            $this->setInternalId($item['internal_id']);
        }

        if (isset($item['internal_reference'])) {
            $this->setInternalReference($item['internal_reference']);
        }

        if (isset($item['sku'])) {
            $this->setSku($item['sku']);
        }

        if (isset($item['name'])) {
            $this->setName($item['name']);
        }

        if (isset($item['description'])) {
            $this->setDescription($item['description']);
        }

        if (isset($item['hs_code'])) {
            $this->setHsCode($item['hs_code']);
        }

        if (isset($item['country_of_origin'])) {
            $this->setCountryOfOrigin($item['country_of_origin']);
        }

        if (isset($item['image_url'])) {
            $this->setImageUrl($item['image_url']);
        }

        if (isset($item['unit_weight'])) {
            $this->setUnitWeight($item['unit_weight']);
        }

        if (isset($item['unit_price_excluding_tax'])) {
            $this->setUnitPriceExcludingTax($item['unit_price_excluding_tax']);
        }

        if (isset($item['unit_price_including_tax'])) {
            $this->setUnitPriceIncludingTax($item['unit_price_including_tax']);
        }

        if (isset($item['quantity'])) {
            $this->setQuantity($item['quantity']);
        }

        if (isset($item['total_price_excluding_tax'])) {
            $this->setTotalPriceExcludingTax($item['total_price_excluding_tax']);
        }

        if (isset($item['total_price_including_tax'])) {
            $this->setTotalPriceIncludingTax($item['total_price_including_tax']);
        }

        if (isset($item['total_tax_amount'])) {
            $this->setTotalTaxAmount($item['total_tax_amount']);
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
     * @return Item
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
     * @return Item
     */
    public function setInternalReference($internal_reference)
    {
        $this->internal_reference = (string) $internal_reference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     * @return Item
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHsCode()
    {
        return $this->hs_code;
    }

    /**
     * @param mixed $hs_code
     * @return Item
     */
    public function setHsCode($hs_code)
    {
        $this->hs_code = $hs_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryOfOrigin()
    {
        return $this->country_of_origin;
    }

    /**
     * @param mixed $country_of_origin
     * @return Item
     */
    public function setCountryOfOrigin($country_of_origin)
    {
        $this->country_of_origin = $country_of_origin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     * @return Item
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnitWeight()
    {
        return $this->unit_weight;
    }

    /**
     * @param mixed $unit_weight
     * @return Item
     */
    public function setUnitWeight($unit_weight)
    {

        $this->unit_weight = is_null($unit_weight) ? null : ((float) $unit_weight);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnitPriceExcludingTax()
    {
        return $this->unit_price_excluding_tax;
    }

    /**
     * @param mixed $unit_price_excluding_tax
     * @return Item
     */
    public function setUnitPriceExcludingTax($unit_price_excluding_tax)
    {
        $this->unit_price_excluding_tax = is_null($unit_price_excluding_tax) ? null : ((float) $unit_price_excluding_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnitPriceIncludingTax()
    {
        return $this->unit_price_including_tax;
    }

    /**
     * @param mixed $unit_price_including_tax
     * @return Item
     */
    public function setUnitPriceIncludingTax($unit_price_including_tax)
    {
        $this->unit_price_including_tax = is_null($unit_price_including_tax) ? null : ((float) $unit_price_including_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = is_null($quantity) ? null : ((float) $quantity);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceExcludingTax()
    {
        return $this->total_price_excluding_tax;
    }

    /**
     * @param mixed $total_price_excluding_tax
     * @return Item
     */
    public function setTotalPriceExcludingTax($total_price_excluding_tax)
    {
        $this->total_price_excluding_tax = is_null($total_price_excluding_tax) ? null : ((float) $total_price_excluding_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceIncludingTax()
    {
        return $this->total_price_including_tax;
    }

    /**
     * @param mixed $total_price_including_tax;
     * @return Item
     */
    public function setTotalPriceIncludingTax($total_price_including_tax)
    {
        $this->total_price_including_tax = is_null($total_price_including_tax) ? null : ((float) $total_price_including_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalTaxAmount()
    {
        return $this->total_tax_amount;
    }

    /**
     * @param mixed $total_tax_amount
     * @return Item
     */
    public function setTotalTaxAmount($total_tax_amount)
    {
        $this->total_tax_amount = is_null($total_tax_amount) ? null : ((float) $total_tax_amount);
        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

}
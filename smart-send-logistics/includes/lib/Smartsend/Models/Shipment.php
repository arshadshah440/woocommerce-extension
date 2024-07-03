<?php

namespace Smartsend\Models;

use Smartsend\Models\Shipment\Sender;
use Smartsend\Models\Shipment\Receiver;
use Smartsend\Models\Shipment\Agent as ShipmentAgent;
use Smartsend\Models\Shipment\Parcel;
use  Smartsend\Models\Shipment\Services;

require_once 'Shipment/Sender.php';
require_once 'Shipment/Receiver.php';
require_once 'Shipment/Agent.php';
require_once 'Shipment/Parcel.php';
require_once 'Shipment/Services.php';

class Shipment implements \JsonSerializable
{
    private $internal_id;
    private $internal_reference;
    private $shipping_carrier;
    private $shipping_method;
    private $shipping_date;
    private $sender;
    private $receiver;
    private $agent;
    private $parcels;
    private $services;
    private $subtotal_price_excluding_tax;
    private $subtotal_price_including_tax;
    private $shipping_price_excluding_tax;
    private $shipping_price_including_tax;
    private $total_price_excluding_tax;
    private $total_price_including_tax;
    private $total_tax_amount;
    private $currency;

    public function __construct(Array $shipment=null)
    {
        if (isset($shipment['internal_id'])) {
            $this->setInternalId($shipment['internal_id']);
        }

        if (isset($shipment['internal_reference'])) {
            $this->setInternalReference($shipment['internal_reference']);
        }

        if (isset($shipment['shipping_carrier'])) {
            $this->setShippingCarrier($shipment['shipping_carrier']);
        }

        if (isset($shipment['shipping_method'])) {
            $this->setShippingMethod($shipment['shipping_method']);
        }

        if (isset($shipment['shipping_date'])) {
            $this->setShippingDate($shipment['shipping_date']);
        }

        if (isset($shipment['sender'])) {
            $this->setSender($shipment['sender']);
        }

        if (isset($shipment['receiver'])) {
            $this->setReceiver($shipment['receiver']);
        }

        if (isset($shipment['agent'])) {
            $this->setAgent($shipment['agent']);
        }

        if (isset($shipment['parcels'])) {
            $this->setParcels($shipment['parcels']);
        }

        if (isset($shipment['services'])) {
            $this->setServices($shipment['services']);
        }

        if (isset($shipment['subtotal_price_excluding_tax'])) {
            $this->setSubtotalPriceExcludingTax($shipment['subtotal_price_excluding_tax']);
        }

        if (isset($shipment['subtotal_price_including_tax'])) {
            $this->setSubtotalPriceIncludingTax($shipment['subtotal_price_including_tax']);
        }

        if (isset($shipment['shipping_price_excluding_tax'])) {
            $this->setShippingPriceExcludingTax($shipment['shipping_price_excluding_tax']);
        }

        if (isset($shipment['shipping_price_including_tax'])) {
            $this->setShippingPriceIncludingTax($shipment['shipping_price_including_tax']);
        }

        if (isset($shipment['total_price_excluding_tax'])) {
            $this->setTotalPriceExcludingTax($shipment['total_price_excluding_tax']);
        }

        if (isset($shipment['total_price_including_tax'])) {
            $this->setTotalPriceIncludingTax($shipment['total_price_including_tax']);
        }

        if (isset($shipment['total_tax_amount'])) {
            $this->setTotalTaxAmount($shipment['total_tax_amount']);
        }

        if (isset($shipment['currency'])) {
            $this->setCurrency($shipment['currency']);
        }

    }

    /**
     * @return null
     */
    public function getInternalId()
    {
        return $this->internal_id;
    }

    /**
     * @param string $internal_id
     * @return Shipment
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
     * @return Shipment
     */
    public function setInternalReference($internal_reference)
    {
        $this->internal_reference = (string) $internal_reference;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingCarrier()
    {
        return $this->shipping_carrier;
    }

    /**
     * @param mixed $shipping_carrier
     * @return Shipment
     */
    public function setShippingCarrier($shipping_carrier)
    {
        $this->shipping_carrier = $shipping_carrier;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingMethod()
    {
        return $this->shipping_method;
    }

    /**
     * @param mixed $shipping_method
     * @return Shipment
     */
    public function setShippingMethod($shipping_method)
    {
        $this->shipping_method = $shipping_method;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getShippingDate()
    {
        return $this->shipping_date;
    }

    /**
     * @param mixed $shipping_date
     * @return Shipment
     */
    public function setShippingDate($shipping_date)
    {
        $this->shipping_date = $shipping_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Sender $sender
     * @return Shipment
     */
    public function setSender(Sender $sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     * @return Shipment
     */
    public function setReceiver(Receiver $receiver)
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param ShipmentAgent $agent
     * @return Shipment
     */
    public function setAgent(ShipmentAgent $agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParcels()
    {
        return $this->parcels;
    }

    /**
     * @param mixed $parcels
     * @return Shipment
     */
    public function setParcels(array $parcels)
    {
        $this->parcels = $parcels;
        return $this;
    }

    /**
     * @param Parcel $parcel
     * @return Shipment
     */
    public function addParcel(Parcel $parcel)
    {
        if (is_array($this->parcels)) {
            $this->parcels[] = $parcel;
        } else {
            $this->setParcels(array($parcel));
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Services $services
     * @return Shipment
     */
    public function setServices(Services $services)
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubtotalPriceExcludingTax()
    {
        return $this->subtotal_price_excluding_tax;
    }

    /**
     * @param mixed $subtotal_price_excluding_tax
     * @return Shipment
     */
    public function setSubtotalPriceExcludingTax($subtotal_price_excluding_tax)
    {
        $this->subtotal_price_excluding_tax = is_null($subtotal_price_excluding_tax) ? null : ((float) $subtotal_price_excluding_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubtotalPriceIncludingTax()
    {
        return $this->subtotal_price_including_tax;
    }

    /**
     * @param mixed $subtotal_price_including_tax
     * @return Shipment
     */
    public function setSubtotalPriceIncludingTax($subtotal_price_including_tax)
    {
        $this->subtotal_price_including_tax = is_null($subtotal_price_including_tax) ? null : ((float) $subtotal_price_including_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingPriceExcludingTax()
    {
        return $this->shipping_price_excluding_tax;
    }

    /**
     * @param mixed $shipping_price_excluding_tax
     * @return Shipment
     */
    public function setShippingPriceExcludingTax($shipping_price_excluding_tax)
    {
        $this->shipping_price_excluding_tax = is_null($shipping_price_excluding_tax) ? null : ((float) $shipping_price_excluding_tax);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShippingPriceIncludingTax()
    {
        return $this->shipping_price_including_tax;
    }

    /**
     * @param mixed $shipping_price_including_tax
     * @return Shipment
     */
    public function setShippingPriceIncludingTax($shipping_price_including_tax)
    {
        $this->shipping_price_including_tax = is_null($shipping_price_including_tax) ? null : ((float) $shipping_price_including_tax);
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
     * @return Shipment
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
     * @param mixed $total_price_including_tax
     * @return Shipment
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
     * @return Shipment
     */
    public function setTotalTaxAmount($total_tax_amount)
    {
        $this->total_tax_amount = is_null($total_tax_amount) ? null : ((float) $total_tax_amount);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return Shipment
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

}
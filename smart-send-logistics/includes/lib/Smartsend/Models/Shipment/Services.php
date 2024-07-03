<?php

namespace Smartsend\Models\Shipment;

class Services  implements \JsonSerializable
{
    private $email_notification;
    private $sms_notification;
    private $flex_delivery;

    public function __construct(Array $services=array())
    {
        if (isset($services['email_notification'])) {
            $this->setEmailNotification($services['email_notification']);
        }

        if (isset($services['sms_notification'])) {
            $this->setSmsNotification($services['sms_notification']);
        }

        if (isset($services['flex_delivery'])) {
            $this->setFlexDelivery($services['flex_delivery']);
        }
    }

    /**
     * @return mixed
     */
    public function getEmailNotification()
    {
        return $this->email_notification;
    }

    /**
     * @param mixed $email_notification
     * @return Services
     */
    public function setEmailNotification($email_notification)
    {
        $this->email_notification = $email_notification;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmsNotification()
    {
        return $this->sms_notification;
    }

    /**
     * @param mixed $sms_notification
     * @return Services
     */
    public function setSmsNotification($sms_notification)
    {
        $this->sms_notification = $sms_notification;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlexDelivery()
    {
        return $this->flex_delivery;
    }

    /**
     * @param mixed $flex_delivery
     * @return Services
     */
    public function setFlexDelivery($flex_delivery)
    {
        $this->flex_delivery = $flex_delivery;
        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

}
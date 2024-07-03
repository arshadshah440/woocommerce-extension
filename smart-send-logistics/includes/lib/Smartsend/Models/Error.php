<?php


namespace Smartsend\Models;


class Error
{
    public $links;
    public $id;
    public $code;
    public $message;
    public $errors;

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param mixed $links
     * @return Error
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Error
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Error
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Error
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     * @return Error
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

}
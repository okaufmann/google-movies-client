<?php

namespace GoogleMovieClient\Models;

class Theater
{
    private $tid;
    private $name;
    private $address;
    private $city;
    private $state;
    private $country;

    /**
     * @var ShowtimeInfo
     */
    private $showtimeInfo;

    public function __construct(ResultItem $resultItem = null)
    {
        if ($resultItem != null) {
            $this->setTid($resultItem->getId());
            $this->setName($resultItem->getName());

            $addressParts = explode(', ', $resultItem->getInfo());

            //TODO: Try Parse full address with phone

            $this->setAddress($addressParts[0]);
            $this->setCity($addressParts[1]);
            if (count($addressParts) == 4) {
                $this->setState($addressParts[2]);
                $cityParts = explode(" - ", $addressParts[3]);
                $this->setCountry($cityParts[0]);
            } else if (count($addressParts) == 3) {
                $cityParts = explode(" - ", $addressParts[2]);
                $this->setCountry($cityParts[0]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @param mixed $tid
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return ShowtimeInfo
     */
    public function getShowtimeInfo()
    {
        return $this->showtimeInfo;
    }

    /**
     * @param ShowtimeInfo $showtimeInfo
     */
    public function setShowtimeInfo($showtimeInfo)
    {
        $this->showtimeInfo = $showtimeInfo;
    }

    /**
     * @return mixed $address
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
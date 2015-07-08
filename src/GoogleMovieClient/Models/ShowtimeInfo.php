<?php

namespace GoogleMovieClient\Models;

class ShowtimeInfo
{
    private $info;
    private $times = [];
    private $showLanguage;

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return array
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param array $times
     */
    public function setTimes($times)
    {
        $this->times = $times;
    }

    /**
     * @return mixed
     */
    public function getShowLanguage()
    {
        return $this->showLanguage;
    }

    /**
     * @param mixed $showLanguage
     */
    public function setShowLanguage($showLanguage)
    {
        $this->showLanguage = $showLanguage;
    }
}

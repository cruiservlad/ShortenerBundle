<?php

namespace Cruiser\ShortenerUrlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ShortenerRepository")
 * @ORM\Table(name="cruiser_newshortener")
 */
class Shortener
{
	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shorturl", type="string", length=32, nullable=true)
     */
    protected $shorturl = null;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    protected $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="hits", type="integer")
     */
    protected $hits = 0;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shorturl
     *
     * @param string $shorturl
     * @return Shortener
     */
    public function setShorturl($shorturl)
    {
        $this->shorturl = $shorturl;

        return $this;
    }

    /**
     * Get shorturl
     *
     * @return string 
     */
    public function getShorturl()
    {
        return $this->shorturl;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Shortener
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Shortener
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set hits
     *
     * @param integer $hits
     * @return Shortener
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Get hits
     *
     * @return integer 
     */
    public function getHits()
    {
        return $this->hits;
    }
}

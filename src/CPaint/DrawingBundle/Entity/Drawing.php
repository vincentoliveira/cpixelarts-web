<?php

namespace CPaint\DrawingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drawing Entity
 *
 * @ORM\Table(name="drawing")
 * @ORM\Entity(repositoryClass="CPaint\DrawingBundle\Repository\DrawingRepository")
 */
class Drawing
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    private $width;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    private $height;
    
    /**
     * @var ArrayCollection
     *     
     * @ORM\OneToMany(targetEntity="CPaint\DrawingBundle\Entity\Pixel", mappedBy="drawing", cascade={"remove", "persist"})
     */
    private $pixels;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->pixels = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Drawing
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        if (empty($this->title) && isset($this->id)) {
            return "#" . $this->id;
        }
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Drawing
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Drawing
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Drawing
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Add pixels
     *
     * @param \CPaint\DrawingBundle\Entity\Pixel $pixels
     * @return Drawing
     */
    public function addPixel(\CPaint\DrawingBundle\Entity\Pixel $pixels)
    {
        $this->pixels[] = $pixels;

        return $this;
    }

    /**
     * Remove pixels
     *
     * @param \CPaint\DrawingBundle\Entity\Pixel $pixels
     */
    public function removePixel(\CPaint\DrawingBundle\Entity\Pixel $pixels)
    {
        $this->pixels->removeElement($pixels);
    }

    /**
     * Get pixels
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPixels()
    {
        return $this->pixels;
    }
}

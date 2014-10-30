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
     * @var String
     *
     * @ORM\Column(name="title", type="string", length=31, nullable=true)
     */
    private $title;
    
    /**
     * @var String
     *
     * @ORM\Column(name="title_canonical", type="string", length=31, nullable=true)
     */
    private $titleCanonical;
    
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
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=false)
     */
    private $isLocked = false;
    
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
     * Set titleCanonical
     *
     * @param string $titleCanonical
     * @return Drawing
     */
    public function setTitleCanonical($titleCanonical)
    {
        $this->titleCanonical = $titleCanonical;

        return $this;
    }

    /**
     * Get titleCanonical
     *
     * @return string 
     */
    public function getTitleCanonical()
    {
        if (empty($this->titleCanonical) && isset($this->id)) {
            return $this->id;
        }
        return $this->titleCanonical;
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
     * Set Locked
     *
     * @param boolean $isLocked
     * @return Drawing
     */
    public function setLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Is Locked ?
     *
     * @return boolean 
     */
    public function IsLocked()
    {
        return $this->isLocked;
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

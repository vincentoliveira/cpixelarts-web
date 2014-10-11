<?php

namespace CPaint\DrawingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pixel Entity
 *
 * @ORM\Table(name="pixel")
 * @ORM\Entity()
 */
class Pixel
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="color", type="integer", nullable=false)
     */
    private $color;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="CPaint\DrawingBundle\Entity\Drawing", inversedBy="pixels")
     * @ORM\JoinColumn(name="drawing_id", referencedColumnName="id", nullable=false)
     */
    private $drawing;


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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Pixel
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
     * Set color
     *
     * @param integer $color
     * @return Pixel
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return integer 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Pixel
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set drawing
     *
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @return Pixel
     */
    public function setDrawing(\CPaint\DrawingBundle\Entity\Drawing $drawing)
    {
        $this->drawing = $drawing;

        return $this;
    }

    /**
     * Get drawing
     *
     * @return \CPaint\DrawingBundle\Entity\Drawing 
     */
    public function getDrawing()
    {
        return $this->drawing;
    }
}

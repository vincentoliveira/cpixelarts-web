<?php

namespace CPaint\DrawingBundle\Service;

use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Entity\Pixel;
use Symfony\Component\DependencyInjection\Container;

/**
 * Service to export binary image from PixelArts
 *
 * @author vincent
 */
class ExportService
{
    
    /**
     * Export width
     * 
     * @var int 
     */
    protected $xWidth;

    /**
     * Export height
     * 
     * @var int 
     */
    protected $xHeight;

    /**
     * Drawing width
     * 
     * @var int 
     */
    protected $dWidth;

    /**
     * Drawing height
     * 
     * @var int 
     */
    protected $dHeight;

    /**
     * Export gif from drawing
     * 
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @param int $width
     * @param int $height
     * @return string filename of exported gif
     */
    public function exportGif(Drawing $drawing, $width = 0, $height = 0)
    {
        $this->dWidth = $drawing->getWidth();
        $this->dHeight = $drawing->getHeight();
        $this->xWidth = $width > 0 ? $width : $this->dWidth;
        $this->xHeight = $height > 0 ? $height : $this->dHeight;

        $image = $this->createImage($drawing);
        
        $tmpfname = tempnam("/tmp", "cpaint");
        imagegif($image, $tmpfname);

        return $tmpfname;
    }
    
    /**
     * Export png from drawing
     * 
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @param int $width
     * @param int $height
     * @return string filename of exported gif
     */
    public function exportPng(Drawing $drawing, $width = 0, $height = 0)
    {
        $this->dWidth = $drawing->getWidth();
        $this->dHeight = $drawing->getHeight();
        $this->xWidth = $width > 0 ? $width : $this->dWidth;
        $this->xHeight = $height > 0 ? $height : $this->dHeight;

        $image = $this->createImage($drawing);
        
        $tmpfname = tempnam("/tmp", "cpaint");
        imagepng($image, $tmpfname);

        return $tmpfname;
    }
    
    private function createImage(Drawing $drawing)
    {
        $image = imagecreatetruecolor($this->xWidth, $this->xHeight);
        //imagesavealpha($image, true);

        // set background to transparent
        imagefill($image, 0, 0, 0x7f010101);
        imagecolortransparent($image, 0x7f010101);

        $colors = [];
        foreach ($drawing->getPixels() as $pixel) {
            $y = intval($pixel->getPosition() / $this->dWidth);
            $x = ($pixel->getPosition() % $this->dWidth);

            $color = $pixel->getColor();
            if (!isset($colors[$color])) {
                $rgb = ColorService::colorToRGB($color);
                $colors[$color] = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
            }

            $this->addPixel($image, $x, $y, $colors[$color]);
        }
        
        return $image;
    }

    /**
     * Add a pixel in $image
     * 
     * @param resource $image
     * @param int $x <p>
     * x-coordinate.
     * </p>
     * @param int $y <p>
     * y-coordinate.
     * </p>
     * @param int $color <p>
     * A color identifier created with
     * <b>imagecolorallocate</b>.
     * </p>
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    protected function addPixel($image, $x, $y, $color)
    {
        $xCoeff = $this->xWidth / $this->dWidth;
        $yCoeff = $this->xHeight / $this->dHeight;

        for ($xx = round($x * $xCoeff); $xx < round(($x + 1) * $xCoeff); $xx++) {
            for ($yy = round($y * $yCoeff); $yy < round(($y + 1) * $yCoeff); $yy++) {
                imagesetpixel($image, $xx, $yy, $color);
            }
        }
    }

}

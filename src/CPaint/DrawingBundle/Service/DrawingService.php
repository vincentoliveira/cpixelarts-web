<?php

namespace CPaint\DrawingBundle\Service;

use CPaint\DrawingBundle\Entity\Drawing;

/**
 * Description of DrawingService
 *
 * @author vincent
 */
class DrawingService
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

        $image = imagecreatetruecolor($this->xWidth, $this->xHeight);

        // set background to white
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        $colors = [];
        foreach ($drawing->getPixels() as $pixel) {
            $y = intval($pixel->getPosition() / $this->dWidth);
            $x = ($pixel->getPosition() % $this->dHeight);

            $color = $pixel->getColor();
            if (!isset($colors[$color])) {
                $rgb = ColorService::colorToRGB($color);
                $colors[$color] = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
            }

            $this->addPixel($image, $x, $y, $colors[$color]);
        }

        $tmpfname = tempnam("/tmp", "cpaint");
        //imagepng($bitmap, $tmpfname);
        imagegif($image, $tmpfname);

        return $tmpfname;
    }

    /**
     * Canonicalize title
     * 
     * @param String $str
     * @return String
     */
    public function canonicalizeTitle($str)
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeAccents($str));
    }

    protected function removeAccents($str, $charset = 'utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
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

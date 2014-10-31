<?php

namespace CPaint\DrawingBundle\Service;

use CPaint\DrawingBundle\Entity\Drawing;
use CPaint\DrawingBundle\Entity\Pixel;
use Symfony\Component\DependencyInjection\Container;

/**
 * Description of DrawingService
 *
 * @author vincent
 */
class DrawingService
{
    public static $allowedSizes = [8, 16, 32, 64];
    public static $defaultSize = 16;

    /**
     *
     * @var Container 
     */
    protected $container;
    
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
     * Constructor
     * 
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Initialise new drawing
     * 
     * @param type $width
     * @param type $height
     * @param type $title
     * @return \CPaint\DrawingBundle\Entity\Drawing
     */
    public function initDrawing($width = -1, $height = -1, $title = null)
    {
        if (!in_array($width, self::$allowedSizes)) {
            $width = self::$defaultSize;
        }
        if (!in_array($height, self::$allowedSizes)) {
            $height = $width;
        }
        
        if ($title) {
            $titleCanonical = $this->canonicalizeTitle($title);
        } else {
            $titleCanonical = null;
        }
        
        $drawing = new Drawing();
        $drawing->setCreatedAt(new \DateTime());
        $drawing->setWidth($width);
        $drawing->setHeight($height);
        $drawing->setTitle($title);
        $drawing->setTitleCanonical($titleCanonical);
        $drawing->setLocked(false);
        
        return $drawing;
    }

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
        //imagesavealpha($image, true);

        // set background to transparent
        imagefill($image, 0, 0, 0x7f000000);
        imagecolortransparent($image, 0x7f000000);

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
        //imagepng($image, $tmpfname);
        imagegif($image, $tmpfname);

        return $tmpfname;
    }

    /**
     * Is drawing displayable
     * 
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @return boolean
     */
    public function isDisplayable(Drawing $drawing)
    {
        return ($drawing->getPixels()->count() > 0 && $drawing->IsLocked()) ||
                ($drawing->getPixels()->count() >= ($drawing->getWidth() * $drawing->getHeight() / 16));
    }

    /**
     * Canonicalize title
     * 
     * @param String $str
     * @return String
     */
    public function canonicalizeTitle($str)
    {
        $repo = $this->container->get('doctrine')->getRepository('CPaintDrawingBundle:Drawing');

        $title = preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeAccents($str));
        
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz0123456789-";
        $salt = "";
        while ($repo->findOneByTitleCanonical($title . $salt) !== null) {
            $salt .= $codeAlphabet[mt_rand(0, strlen($codeAlphabet) - 1)];
        }
        
        return $title . $salt;
    }

    /**
     * Add pixel to a drawing. Return null on error
     * 
     * @param \CPaint\DrawingBundle\Entity\Drawing $drawing
     * @param type $color
     * @param type $position
     * @return \CPaint\DrawingBundle\Entity\Pixel|null
     */
    public function addPixelToDrawing(Drawing $drawing, $color, $position)
    {
        if ($drawing === null || $color < 0 || $color >= 256 || $position < 0 || 
                $position >= $drawing->getWidth() * $drawing->getHeight()) {
            return null;
        }
        
        foreach ($drawing->getPixels() as $pixel) {
            if ($pixel->getPosition() == $position) {
                return null;
            }
        }
        
        $pixel = new Pixel();
        $pixel->setColor($color);
        $pixel->setDrawing($drawing);
        $pixel->setPosition($position);
        
        return $pixel;
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

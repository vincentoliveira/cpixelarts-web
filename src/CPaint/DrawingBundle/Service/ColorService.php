<?php

namespace CPaint\DrawingBundle\Service;

/**
 * Description of ColorService
 *
 * @author vincent
 */
class ColorService
{
    /**
     * 8bit color to RGB
     * 
     * @param int $color
     * @return array keys['r','g','b'] 
     */
    public static function colorToRGB($color)
    {
        $r = ($color >> 5) << 5;
        $g = (($color & 0x1f) >> 2) << 5;
        $b = ($color & 0x03) << 6;
        
        return array(
            'r' => $r,
            'g' => $g,
            'b' => $b,
        );
    }
}

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
//        $r = ($color >> 5) << 5;
//        $g = (($color & 0x1f) >> 2) << 5;
//        $b = ($color & 0x03) << 6;

        $r = $color >> 5;
        $rr = (($r << 6) + ($r << 3) + $r) % 256;
        
        $g = ($color & 0x1f) >> 2;
        $gg = (($g << 6) + ($g << 3) + $g) % 256;
        
        $b = $color & 0x03;
        $bb = ($b << 6) + ($b << 4) + ($b << 2) + $b;
        
        return array(
            'r' => $rr,
            'g' => $gg,
            'b' => $bb,
        );
    }
    
    /**
     * 8bit color to RGB
     * 
     * @param int $color
     * @return array keys['r','g','b'] 
     */
    public static function colorToRGBString($color)
    {
        $rgb = self::colorToRGB($color);
        return sprintf("%02x%02x%02x", $rgb['r'], $rgb['g'], $rgb['b']);
    }
    
    /**
     * Get all RGB colors
     * 
     * @return array
     */
    public static function RGBColors()
    {
        $colors = [0];
        for ($color = 1; $color < 256; ++$color) {
            $colors[$color] = self::colorToRGB($color);
        }
        
        return $colors;
    }
    /**
     * Get all RGB colors
     * 
     * @return array
     */
    public static function RGBStringColors()
    {
        $colors = [];
        for ($color = 0; $color < 256; ++$color) {
            $colors[$color] = self::colorToRGBString($color);
        }
        
        return $colors;
    }
}

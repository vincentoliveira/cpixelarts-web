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
        $colors = [0];
        for ($color = 1; $color < 256; ++$color) {
            $colors[$color] = self::colorToRGBString($color);
        }
        
        return $colors;
    }
}

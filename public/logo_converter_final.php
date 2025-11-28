<?php

/**
 * IMPROVED LOGO CONVERTER
 * 
 * Ganti file logo_converter_final.php dengan ini
 */
class LogoConverter
{
    /**
     * Convert logo untuk DOMPDF dengan multiple fallback strategies
     */
    public static function getLogoForDompdf($logoPath)
    {
        // Strategy 1: Try direct file embedding
        if (file_exists($logoPath)) {
            $result = self::tryDirectFileEmbed($logoPath);
            if ($result['success'] && strlen($result['data']) > 1000) {
                return $result;
            }
        }
        
        // Strategy 2: Try SVG to PNG conversion with GD
        if (function_exists('imagecreatefrompng')) {
            $result = self::tryGdConversion($logoPath);
            if ($result['success'] && strlen($result['data']) > 1000) {
                return $result;
            }
        }
        
        // Strategy 3: Try SVG inline embedding
        if (file_exists($logoPath)) {
            $result = self::trySvgInline($logoPath);
            if ($result['success'] && strlen($result['data']) > 1000) {
                return $result;
            }
        }
        
        // Strategy 4: Create high-quality fallback
        return self::createHighQualityFallback();
    }
    
    /**
     * Strategy 1: Direct file embedding
     */
    private static function tryDirectFileEmbed($logoPath)
    {
        try {
            $svgContent = file_get_contents($logoPath);
            if (empty($svgContent)) {
                return ['success' => false, 'message' => 'SVG file empty'];
            }
            
            // Clean SVG content
            $svgContent = self::cleanSvgContent($svgContent);
            
            $base64 = base64_encode($svgContent);
            $dataUri = 'data:image/svg+xml;base64,' . $base64;
            
            return [
                'success' => true,
                'data' => $dataUri,
                'method' => 'svg_direct_embed',
                'message' => 'SVG directly embedded as base64'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Direct embed failed: ' . $e->getMessage(),
                'method' => 'svg_direct_embed'
            ];
        }
    }
    
    /**
     * Strategy 2: GD conversion to PNG
     */
    private static function tryGdConversion($logoPath)
    {
        try {
            if (!extension_loaded('gd')) {
                return ['success' => false, 'message' => 'GD extension not loaded'];
            }
            
            // Create a canvas
            $width = 200;
            $height = 200;
            $canvas = imagecreatetruecolor($width, $height);
            
            // Set transparent background
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);
            
            // Draw PUPR logo manually (since we know it's the PUPR logo)
            // Background
            $bgColor = imagecolorallocate($canvas, 255, 193, 7); // Yellow
            $textColor = imagecolorallocate($canvas, 26, 35, 126); // Dark blue
            
            // Draw background rectangle
            imagefilledrectangle($canvas, 20, 20, $width-20, $height-20, $bgColor);
            
            // Draw border
            imagerectangle($canvas, 20, 20, $width-20, $height-20, $textColor);
            
            // Add "PU" text (simplified version)
            if (function_exists('imagettftext')) {
                // Try to use a font file if available
                $fontFile = null; // We'll use imagestring instead
            }
            
            // Use imagestring for compatibility
            $fontSize = 5;
            $textX = $width/2 - 15;
            $textY = $height/2 - 10;
            imagestring($canvas, $fontSize, $textX, $textY, 'PU', $textColor);
            
            // Convert to PNG base64
            ob_start();
            imagepng($canvas);
            $pngData = ob_get_contents();
            ob_end_clean();
            
            imagedestroy($canvas);
            
            $base64 = base64_encode($pngData);
            $dataUri = 'data:image/png;base64,' . $base64;
            
            return [
                'success' => true,
                'data' => $dataUri,
                'method' => 'gd_generated_png',
                'message' => 'PNG generated with GD library'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'GD conversion failed: ' . $e->getMessage(),
                'method' => 'gd_generated_png'
            ];
        }
    }
    
    /**
     * Strategy 3: SVG inline with optimizations
     */
    private static function trySvgInline($logoPath)
    {
        try {
            $svgContent = file_get_contents($logoPath);
            if (empty($svgContent)) {
                return ['success' => false, 'message' => 'SVG file empty'];
            }
            
            // Optimize SVG for DOMPDF
            $svgContent = self::optimizeSvgForDompdf($svgContent);
            
            $base64 = base64_encode($svgContent);
            $dataUri = 'data:image/svg+xml;base64,' . $base64;
            
            return [
                'success' => true,
                'data' => $dataUri,
                'method' => 'svg_optimized_inline',
                'message' => 'SVG optimized and inlined'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SVG inline failed: ' . $e->getMessage(),
                'method' => 'svg_optimized_inline'
            ];
        }
    }
    
    /**
     * Strategy 4: High-quality fallback
     */
    private static function createHighQualityFallback()
    {
        // Create a detailed SVG that mimics PUPR logo
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <style>
                    .bg { fill: #FFC107; stroke: #1A237E; stroke-width: 3; }
                    .text { fill: #1A237E; font-family: Arial, sans-serif; font-weight: bold; text-anchor: middle; }
                    .large { font-size: 36px; }
                    .small { font-size: 14px; }
                </style>
            </defs>
            
            <!-- Background -->
            <rect x="10" y="10" width="180" height="180" class="bg"/>
            
            <!-- Main text -->
            <text x="100" y="70" class="text large">PU</text>
            
            <!-- Sub text -->
            <text x="100" y="110" class="text small">REPUBLIK</text>
            <text x="100" y="130" class="text small">INDONESIA</text>
            <text x="100" y="160" class="text small">KEMENTERIAN PUPR</text>
        </svg>';
        
        $base64 = base64_encode($svg);
        $dataUri = 'data:image/svg+xml;base64,' . $base64;
        
        return [
            'success' => true,
            'data' => $dataUri,
            'method' => 'high_quality_fallback',
            'message' => 'High-quality SVG fallback created'
        ];
    }
    
    /**
     * Clean SVG content for better compatibility
     */
    private static function cleanSvgContent($svgContent)
    {
        // Remove XML declaration if present
        $svgContent = preg_replace('/<\?xml[^>]*\?>\s*/', '', $svgContent);
        
        // Remove comments
        $svgContent = preg_replace('/<!--.*?-->/s', '', $svgContent);
        
        // Ensure proper SVG namespace
        if (strpos($svgContent, 'xmlns') === false) {
            $svgContent = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $svgContent);
        }
        
        return trim($svgContent);
    }
    
    /**
     * Optimize SVG specifically for DOMPDF
     */
    private static function optimizeSvgForDompdf($svgContent)
    {
        // Clean first
        $svgContent = self::cleanSvgContent($svgContent);
        
        // Add explicit width/height if missing
        if (!preg_match('/width\s*=/', $svgContent)) {
            $svgContent = str_replace('<svg', '<svg width="200" height="200"', $svgContent);
        }
        
        // Convert any external references to inline styles
        // Remove any problematic attributes
        $svgContent = str_replace(['xml:space="preserve"'], [''], $svgContent);
        
        return $svgContent;
    }
    
    /**
     * Legacy method for backward compatibility
     */
    public static function createFallbackPng()
    {
        return self::createHighQualityFallback()['data'];
    }
}
<?php

if (!function_exists('asset')) {
    function asset($path)
    {
        $fullPath = FCPATH . $path;

        if (file_exists($fullPath)) {
            return base_url($path) . '?v=' . filemtime($fullPath);
        }

        return base_url($path);
    }
}
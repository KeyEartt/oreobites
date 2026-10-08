<?php

if (!function_exists('img_url')) {
    /**
     * Returns a displayable image URL.
     * - null/empty         → null
     * - starts with http   → returned as-is (external link)
     * - anything else      → passed through asset() (relative path)
     */
    function img_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return asset($path);
    }
}
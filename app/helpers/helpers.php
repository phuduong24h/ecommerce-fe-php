<?php
// app/Helpers/helpers.php

if (!function_exists('active')) {
    /**
     * Helper để highlight menu active
     * @param string|array $routes - tên route hoặc mảng routes
     * @return string - class 'active' nếu match, '' nếu không
     */
    function active($routes): string
    {
        // Chuyển string thành array nếu cần
        $routes = is_array($routes) ? $routes : explode(',', $routes);
        
        // Kiểm tra route hiện tại có match không
        return request()->routeIs($routes) ? 'active' : '';
    }
}
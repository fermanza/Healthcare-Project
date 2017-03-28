<?php

if (! function_exists('route_starts_with')) {
    /**
     * Determines if current route name starts with given string.
     *
     * @param  string  $routeName
     * @param  string  $output
     * @return string
     */
    function route_starts_with($routeName, $output = 'active')
    {
        $currentRouteName = Route::currentRouteName();

        if (starts_with($currentRouteName, $routeName)) {
            return $output;
        }

        return '';
    }
}

if (! function_exists('route_is_active')) {
    /**
     * Determines if given route name is equal to current one.
     *
     * @param  string|array  $routeName
     * @param  string  $output
     * @return string
     */
    function route_is_active($routeName, $output = 'active')
    {
        $routeNames = (array) $routeName;
        $currentRouteName = Route::currentRouteName();

        foreach ($routeNames as $rName) {
            if ($currentRouteName == $rName) {
                return $output;
            }
        }

        return '';
    }
}

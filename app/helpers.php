<?php

use Illuminate\Support\HtmlString;

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

if (! function_exists('flash')) {
    /**
     * Flashes to the session.
     *
     * @param  string  $message
     * @param  string  $type
     * @return string
     */
    function flash($message, $type = 'success')
    {
        Session::flash('flash-message', ['type' => $type, 'message' => $message]);
    }
}

if (! function_exists('sort_column_url')) {
    /**
     * Get sorting link for given column and label.
     *
     * @param  string  $column
     * @param  string  $label
     * @return \Illuminate\Support\HtmlString
     */
    function sort_column_link($column, $label)
    {
        if (Request::input('sort') == $column && Request::input('order') == 'asc') {
            $order = 'desc';
            $icon = '<i class="fa fa-sort-asc sort-caret"></i>';
        } elseif (Request::input('sort') == $column && Request::input('order') == 'desc') {
            $order = 'asc';
            $icon = '<i class="fa fa-sort-desc sort-caret"></i>';
        } else {
            $order = 'asc';
            $icon = '<i class="fa fa-sort sort-caret text-muted"></i>';
        }

        $url =  Request::fullUrlWithQuery(['sort' => $column, 'order' => $order]);

        $link = "<a href=\"{$url}\" class=\"pagination-link\">{$label} {$icon}</a>";

        return new HtmlString($link);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    /**
     * Store in Session Sidebar status collapsed.
     *
     * @return \Illuminate\Http\Response
     */
    public function collapse()
    {
        session(['sidebar-collapsed' => true]);

        return response()->json(['sidebar' => 'collapsed']);
    }

    /**
     * Store in Session Sidebar status expanded.
     *
     * @return \Illuminate\Http\Response
     */
    public function expand()
    {
        session(['sidebar-collapsed' => false]);

        return response()->json(['sidebar' => 'expanded']);
    }
}

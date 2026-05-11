<?php

namespace App\Controllers;

class HeaderCont extends BaseController
{
    public function search()
    {
        $query = $this->request->getGet('q');

        // Empty search function for now
        // Normally this would query the database based on $query

        // For now, we'll just return a string or you can redirect/show a view later
        return "Search results for: " . esc($query);
    }
}

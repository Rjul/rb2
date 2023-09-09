<?php

namespace App\Http\Controllers;

use App\Models\PageAdmin;

class PageAdminController extends Controller
{

    public function index(PageAdmin $pageAdmin)
    {
        return view('pages.page-admin.page-admin', [
            'pageAdmin' => $pageAdmin
        ]);
    }

}

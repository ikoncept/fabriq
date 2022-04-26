<?php

namespace Ikoncept\Fabriq\Http\Controllers;

use Ikoncept\Fabriq\Models\Page;
use Illuminate\Routing\Controller;

class PermaLinkRedirectController extends Controller
{
    public function __invoke(string $sha)
    {
        $page = Page::whereHash($sha)->firstOrFail();

        return '';
    }
}

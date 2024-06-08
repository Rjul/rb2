<?php

namespace App\Http\Controllers;

use App\Models\Emision;
use App\Models\Programme;
use App\Utilities\RssService;
use Illuminate\Http\Request;

class RssController extends Controller
{
    public function show(Programme $programme)
    {
        if (!$programme->has_rss) {
            return response('', 403);
        }
        $rssService = new RssService();

        if (!$rssService->hasRssContentForProgramme($programme)) {
            $rssService->generateForProgramme($programme);
        }

        return response(
            $rssService->getRssContentForProgramme($programme),
            200,
            ['Content-Type' => 'application/xml']
        );
    }
}

<?php

namespace App\Utilities;

use App\Models\Programme;
use Illuminate\Support\Facades\Storage;

class RssService
{

    public function generateAll(): bool
    {
        $programmes = Programme::with('emisions')
            ->where('has_rss', true)
            ->get();
        $isFullSuccess = true;
        foreach ($programmes as $programme) {
            try {
                $this->generateForProgramme($programme);
            } catch (\Throwable $throwable) {
                $isFullSuccess = false;
                dump($throwable->getMessage());
            }
        }
        return $isFullSuccess;
    }

    public function generateForProgramme(Programme $programme): bool
    {
        if (!$this->isProcessable($programme)) {
            return false;
        }

        $content = view('rss/base', ['programme' => $programme])->render();

         return file_put_contents( base_path().'/public/rss/'.$programme->slug.'.xml', $content);
    }

    public function hasRssContentForProgramme(Programme $programme)
    {
        return file_exists(base_path().'/public/rss/'.$programme->slug.'.xml');
    }

    public function getRssContentForProgramme(Programme $programme)
    {
        return file_get_contents( base_path().'/public/rss/'.$programme->slug.'.xml');
    }

    private function isProcessable(Programme $programme)
    {
        return !empty($programme->image) && !empty($programme->description) && !empty($programme->user);
    }
}

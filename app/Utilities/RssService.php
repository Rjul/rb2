<?php

namespace App\Utilities;

use App\Models\Programme;
use Illuminate\Support\Facades\Storage;

class RssService
{

    public function generateAll(): bool
    {
        $programmes = Programme::with('emisions')->get();
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

    private function generateForProgramme(Programme $programme): bool
    {
        if (!$this->isProcessable($programme)) {
            return false;
        }

        $content = view('rss/base', ['programme' => $programme]);

         return file_put_contents( base_path().'/public/rss/'.$programme->slug.'.xml', $content);
    }

    private function isProcessable(Programme $programme)
    {
        return !empty($programme->image) && !empty($programme->description) && !empty($programme->user);
    }
}

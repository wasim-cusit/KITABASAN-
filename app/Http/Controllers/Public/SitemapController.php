<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $courses = Book::where('status', 'published')
            ->select('id', 'slug', 'updated_at')
            ->latest('updated_at')
            ->get();

        $siteUrl = config('app.url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Home page
        $xml .= $this->urlElement($siteUrl, '1.0', 'daily');

        // Courses index
        $xml .= $this->urlElement($siteUrl . '/courses', '0.9', 'daily');

        // About page
        $xml .= $this->urlElement($siteUrl . '/about', '0.8', 'monthly');

        // Contact page
        $xml .= $this->urlElement($siteUrl . '/contact', '0.8', 'monthly');

        // Individual courses
        foreach ($courses as $course) {
            $url = $siteUrl . '/courses/' . $course->id;
            if ($course->slug) {
                $url = $siteUrl . '/courses/' . $course->slug;
            }
            $lastmod = $course->updated_at->format('Y-m-d');
            $xml .= $this->urlElement($url, '0.7', 'weekly', $lastmod);
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function urlElement($url, $priority, $changefreq, $lastmod = null)
    {
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '    <priority>' . $priority . '</priority>' . "\n";
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
        if ($lastmod) {
            $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
        }
        $xml .= '  </url>' . "\n";
        return $xml;
    }
}

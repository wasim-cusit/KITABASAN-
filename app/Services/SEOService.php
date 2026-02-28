<?php

namespace App\Services;

use App\Models\Book;
use App\Models\SystemSetting;

class SEOService
{
    /**
     * Generate SEO meta tags for a page
     */
    public static function generateMetaTags(array $data = []): array
    {
        $siteName = SystemSetting::getValue('site_name', config('app.name'));
        $siteUrl = SystemSetting::getValue('site_url', config('app.url'));
        $siteDescription = SystemSetting::getValue('site_description', 'KITAB ASAN - Your trusted learning platform for quality education.');

        $defaults = [
            'title' => $siteName,
            'description' => $siteDescription,
            'keywords' => 'online learning, courses, education, e-learning, kitabasan, online courses, study online',
            'image' => asset('logo.jpeg'),
            'url' => url()->current(),
            'type' => 'website',
            'site_name' => $siteName,
        ];

        $meta = array_merge($defaults, $data);

        // Ensure title doesn't exceed 60 characters
        $meta['title'] = mb_substr($meta['title'], 0, 60);

        // Ensure description doesn't exceed 160 characters
        $meta['description'] = mb_substr($meta['description'], 0, 160);

        return $meta;
    }

    /**
     * Generate structured data (JSON-LD) for a course
     */
    public static function generateCourseSchema(Book $course): array
    {
        $siteUrl = SystemSetting::getValue('site_url', config('app.url'));
        $instructor = $course->teacher;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course->title,
            'description' => $course->description ?? $course->short_description,
            'provider' => [
                '@type' => 'Organization',
                'name' => SystemSetting::getValue('site_name', config('app.name')),
                'url' => $siteUrl,
            ],
            'url' => route('courses.show', $course->id),
        ];

        if ($course->cover_image) {
            $path = ltrim(str_replace('\\', '/', $course->cover_image), '/');
            $schema['image'] = route('storage.serve', ['path' => $path]);
        }

        if ($instructor) {
            $schema['instructor'] = [
                '@type' => 'Person',
                'name' => $instructor->name,
            ];
        }

        if ($course->is_free) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'PKR',
            ];
        } else {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) $course->price,
                'priceCurrency' => SystemSetting::getValue('default_currency', 'PKR'),
            ];
        }

        if ($course->subject) {
            $schema['about'] = [
                '@type' => 'Thing',
                'name' => $course->subject->name,
            ];
        }

        return $schema;
    }

    /**
     * Generate Organization schema
     */
    public static function generateOrganizationSchema(): array
    {
        $siteName = SystemSetting::getValue('site_name', config('app.name'));
        $siteUrl = SystemSetting::getValue('site_url', config('app.url'));
        $siteEmail = SystemSetting::getValue('site_email', config('mail.from.address'));

        return [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => asset('logo.jpeg'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => $siteEmail,
                'contactType' => 'Customer Service',
                'telephone' => '+923342372772',
            ],
            'sameAs' => [
                // Add social media links here if available
            ],
        ];
    }

    /**
     * Generate BreadcrumbList schema
     */
    public static function generateBreadcrumbSchema(array $items): array
    {
        $siteUrl = SystemSetting::getValue('site_url', config('app.url'));

        $breadcrumbItems = [];
        $position = 1;

        foreach ($items as $item) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? $siteUrl,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
    }

    /**
     * Generate WebSite schema with search action
     */
    public static function generateWebSiteSchema(): array
    {
        $siteName = SystemSetting::getValue('site_name', config('app.name'));
        $siteUrl = SystemSetting::getValue('site_url', config('app.url'));

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $siteUrl . '/courses?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
}

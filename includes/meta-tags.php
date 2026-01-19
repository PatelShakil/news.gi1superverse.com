<?php
/**
 * SEO Meta Tags Generator
 */

function generateMetaTags($news = null, $isHomePage = false)
{
    if ($isHomePage) {
        $title = SITE_NAME . ' - ' . SITE_DESCRIPTION;
        $description = SITE_DESCRIPTION;
        $keywords = SITE_KEYWORDS;
        $image = SITE_URL . '/assets/images/og-image.jpg';
        $url = SITE_URL;
    } else {
        $title = ($news['meta_title'] ?? $news['title']) . ' | ' . SITE_NAME;
        $description = $news['meta_description'] ?? substr(strip_tags($news['content'] ?? ''), 0, 160);
        $keywords = $news['tags'] ?? SITE_KEYWORDS;
        $image = $news['img_url'] ?? SITE_URL . '/assets/images/og-image.jpg';

        // Fix image URL if it's relative
        if (!empty($image) && !str_starts_with($image, 'http')) {
            $image = STORAGE_URL . $image;
        }

        $url = SITE_URL . '/news/' . ($news['slug'] ?? '');
    }

    // Basic Meta Tags
    echo "<title>{$title}</title>\n";
    echo "<meta name=\"description\" content=\"{$description}\">\n";
    echo "<meta name=\"keywords\" content=\"{$keywords}\">\n";

    // Open Graph Tags
    echo "<meta property=\"og:title\" content=\"{$title}\">\n";
    echo "<meta property=\"og:description\" content=\"{$description}\">\n";
    echo "<meta property=\"og:image\" content=\"{$image}\">\n";
    echo "<meta property=\"og:url\" content=\"{$url}\">\n";
    echo "<meta property=\"og:type\" content=\"" . ($isHomePage ? 'website' : 'article') . "\">\n";
    echo "<meta property=\"og:site_name\" content=\"" . SITE_NAME . "\">\n";

    // Twitter Card Tags
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    echo "<meta name=\"twitter:title\" content=\"{$title}\">\n";
    echo "<meta name=\"twitter:description\" content=\"{$description}\">\n";
    echo "<meta name=\"twitter:image\" content=\"{$image}\">\n";

    // JSON-LD Structured Data
    if (!$isHomePage && $news) {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news['title'],
            'image' => $image,
            'datePublished' => $news['created_at'],
            'dateModified' => $news['updated_at'] ?? $news['created_at'],
            'author' => [
                '@type' => 'Organization',
                'name' => SITE_NAME
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => SITE_NAME,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => SITE_URL . '/assets/images/logo.png'
                ]
            ],
            'description' => $description
        ];

        echo "<script type=\"application/ld+json\">\n";
        echo json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "\n</script>\n";
    }
}

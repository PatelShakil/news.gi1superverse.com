<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/api-client.php';
require_once __DIR__ . '/includes/meta-tags.php';

// Get slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /news');
    exit;
}

// Fetch news by slug
$response = getNewsBySlug($slug);

if (!$response || !$response['status']) {
    header('HTTP/1.0 404 Not Found');
    echo '404 - News Not Found';
    exit;
}

$news = $response['data'];
$recommended = $news['recommended'] ?? [];

// Fix image URL
$imageUrl = $news['img_url'] ?? '';
if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
    $imageUrl = STORAGE_URL . $imageUrl;
}

$pageTitle = $news['title'];
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<head>
    <?php generateMetaTags($news, false); ?>
</head>

<!-- Article Container -->
<article class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <li><a href="/" class="hover:text-blue-600 dark:hover:text-blue-400">Home</a></li>
            <li>/</li>
            <li><a href="/news" class="hover:text-blue-600 dark:hover:text-blue-400">News</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white"><?= htmlspecialchars($news['category']) ?></li>
        </ol>
    </nav>
    
    <!-- Article Header -->
    <header class="mb-8">
        <!-- Category & Date -->
        <div class="flex items-center gap-4 mb-4">
            <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 px-4 py-1 rounded-full text-sm font-semibold uppercase">
                <?= htmlspecialchars($news['category']) ?>
            </span>
            <?php if ($news['is_featured'] ?? false): ?>
                <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 px-4 py-1 rounded-full text-sm font-semibold">
                    Featured
                </span>
            <?php endif; ?>
            <span class="text-gray-600 dark:text-gray-400 text-sm">
                <?= date('F d, Y', strtotime($news['created_at'])) ?>
            </span>
            <?php if (!empty($news['reading_time'])): ?>
                <span class="text-gray-600 dark:text-gray-400 text-sm">
                    • <?= $news['reading_time'] ?> min read
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
            <?= htmlspecialchars($news['title']) ?>
        </h1>
        
        <!-- Tags -->
        <?php if (!empty($news['tags'])): ?>
            <div class="flex flex-wrap gap-2 mb-6">
                <?php foreach (explode(',', $news['tags']) as $tag): ?>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm">
                        #<?= htmlspecialchars(trim($tag)) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
      
    </header>
    
    <!-- Featured Image -->
    <?php if ($imageUrl): ?>
        <figure class="mb-8">
            <img 
                src="<?= htmlspecialchars($imageUrl) ?>" 
                alt="<?= htmlspecialchars($news['title']) ?>"
                class="w-full h-auto rounded-lg shadow-lg"
            >
        </figure>
    <?php endif; ?>

      <!-- Social Share Buttons -->
        <div class="flex flex-wrap items-center gap-3 pb-6 border-b border-gray-200 dark:border-gray-700">
            <span class="text-gray-700 dark:text-gray-300 font-medium">Share:</span>
            
            <button 
                onclick="shareOnFacebook()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
                title="Share on Facebook"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </button>
            
            <button 
                onclick="shareOnTwitter()"
                class="flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors duration-200"
                title="Share on Twitter"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
                Twitter/X
            </button>
            
            <button 
                onclick="shareOnLinkedIn()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors duration-200"
                title="Share on LinkedIn"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
            </button>
            
            <button 
                onclick="shareOnWhatsApp()"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200"
                title="Share on WhatsApp"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                WhatsApp
            </button>
            
            <button 
                onclick="copyLink()"
                class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200"
                title="Copy Link"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Copy
            </button>
        </div>
    
    <!-- Reading Progress Bar -->
    <div id="reading-progress" class="fixed top-0 left-0 h-1 bg-blue-600 z-50 transition-all duration-150" style="width: 0%"></div>
    
    <!-- Article Content -->
    <div class="prose prose-lg dark:prose-invert max-w-none mb-12">
        <?php
        if (!empty($news['content_html'])) {
            // Sanitize and display HTML content
            echo $news['content_html'];
        }
        ?>
    </div>
    
    <!-- Article Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-700 pt-8">
        <!-- Like Button -->
        <div class="flex items-center gap-4 mb-8">
            <button 
                onclick="likeArticle()"
                id="like-btn"
                class="flex items-center gap-2 px-6 py-3 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors duration-200"
            >
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span id="like-count"><?= $news['likes'] ?? 0 ?></span>
                <span>Likes</span>
            </button>
        </div>
        
        <!-- Share Again -->
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enjoyed this article? Share it!</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="shareOnFacebook()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Facebook</button>
                <button onclick="shareOnTwitter()" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors">Twitter</button>
                <button onclick="shareOnLinkedIn()" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors">LinkedIn</button>
                <button onclick="shareOnWhatsApp()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">WhatsApp</button>
            </div>
        </div>
    </footer>
</article>

<!-- Related News -->
<?php if (!empty($recommended)): ?>
    <section class="bg-gray-100 dark:bg-gray-800 py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Related News</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recommended as $relatedNews): ?>
                    <?php
                    $relatedImageUrl = $relatedNews['img_url'] ?? '';
                    if (!empty($relatedImageUrl) && !str_starts_with($relatedImageUrl, 'http')) {
                        $relatedImageUrl = STORAGE_URL . $relatedImageUrl;
                    }
                    ?>
                    <article class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <a href="/news/<?= htmlspecialchars($relatedNews['slug']) ?>">
                            <div class="relative h-40 overflow-hidden">
                                <?php if ($relatedImageUrl): ?>
                                    <img 
                                        src="<?= htmlspecialchars($relatedImageUrl) ?>" 
                                        alt="<?= htmlspecialchars($relatedNews['title']) ?>"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                        loading="lazy"
                                    >
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-white text-3xl font-bold"><?= substr($relatedNews['title'], 0, 1) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        
                        <div class="p-4">
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">
                                <?= htmlspecialchars($relatedNews['category']) ?>
                            </span>
                            <a href="/news/<?= htmlspecialchars($relatedNews['slug']) ?>">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2 line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <?= htmlspecialchars($relatedNews['title']) ?>
                                </h3>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Share Script -->
<script>
// Global variables for analytics and sharing
window.newsId = <?= $news['id'] ?>;
window.apiBaseUrl = '<?= API_BASE_URL ?>';

const newsSlug = '<?= htmlspecialchars($news['slug']) ?>';
const newsTitle = '<?= htmlspecialchars($news['title']) ?>';
const newsId = <?= $news['id'] ?>;
const baseUrl = '<?= SITE_URL ?>/news/' + newsSlug;

function getShareUrl(platform) {
    const params = new URLSearchParams({
        utm_source: platform,
        utm_medium: 'social',
        utm_campaign: 'news_share',
        utm_content: newsSlug
    });
    return baseUrl + '?' + params.toString();
}

function shareOnFacebook() {
    const url = getShareUrl('facebook');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
    trackShare('facebook');
}

function shareOnTwitter() {
    const url = getShareUrl('twitter');
    window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(newsTitle)}`, '_blank', 'width=600,height=400');
    trackShare('twitter');
}

function shareOnLinkedIn() {
    const url = getShareUrl('linkedin');
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
    trackShare('linkedin');
}

function shareOnWhatsApp() {
    const url = getShareUrl('whatsapp');
    window.open(`https://wa.me/?text=${encodeURIComponent(newsTitle + ' ' + url)}`, '_blank');
    trackShare('whatsapp');
}

function copyLink() {
    const url = getShareUrl('copy_link');
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
        trackShare('copy_link');
    });
}

function trackShare(platform) {
    fetch('<?= API_BASE_URL ?>/news/track-share/' + newsId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ platform: platform })
    });
}

function likeArticle() {
    fetch('<?= API_BASE_URL ?>/news/like/' + newsId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            document.getElementById('like-count').textContent = data.data.likes;
            document.getElementById('like-btn').classList.add('animate-pulse');
            setTimeout(() => {
                document.getElementById('like-btn').classList.remove('animate-pulse');
            }, 1000);
        }
    });
}

// Reading Progress
window.addEventListener('scroll', () => {
    const article = document.querySelector('article');
    const scrollTop = window.pageYOffset;
    const docHeight = article.offsetHeight;
    const winHeight = window.innerHeight;
    const scrollPercent = scrollTop / (docHeight - winHeight);
    const scrollPercentRounded = Math.round(scrollPercent * 100);
    
    document.getElementById('reading-progress').style.width = scrollPercentRounded + '%';
});

// Track analytics on page load
window.addEventListener('load', () => {
    console.log("ANALYTICS LOADING")
    trackAnalytics();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

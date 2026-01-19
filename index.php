<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/api-client.php';
require_once __DIR__ . '/includes/meta-tags.php';

// Get filters from query params
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Fetch news
$params = [
    'is_active' => 1,
];

if ($search) {
    $params['search'] = $search;
}

if ($category) {
    $params['category'] = $category;
}

$response = getAllNews($params);
$newsList = $response['data'] ?? [];

// Pagination (simple client-side for now)
$totalNews = count($newsList);
$newsPerPage = NEWS_PER_PAGE;
$totalPages = ceil($totalNews / $newsPerPage);
$offset = ($page - 1) * $newsPerPage;
$paginatedNews = array_slice($newsList, $offset, $newsPerPage);

$pageTitle = 'All News';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<head>
    <?php generateMetaTags(null, true); ?>
</head>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Latest News</h1>
        <p class="text-xl opacity-90">Stay updated with the latest stories and updates</p>
    </div>
</section>

<!-- Search and Filter -->
<section class="container mx-auto px-4 py-8">
    <form method="GET" action="/news" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input 
                    type="text" 
                    name="search" 
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search news..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
            </div>
            
            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select 
                    name="category"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All Categories</option>
                    <option value="technology" <?= $category === 'technology' ? 'selected' : '' ?>>Technology</option>
                    <option value="business" <?= $category === 'business' ? 'selected' : '' ?>>Business</option>
                    <option value="entertainment" <?= $category === 'entertainment' ? 'selected' : '' ?>>Entertainment</option>
                    <option value="sports" <?= $category === 'sports' ? 'selected' : '' ?>>Sports</option>
                    <option value="health" <?= $category === 'health' ? 'selected' : '' ?>>Health</option>
                    <option value="science" <?= $category === 'science' ? 'selected' : '' ?>>Science</option>
                    <option value="politics" <?= $category === 'politics' ? 'selected' : '' ?>>Politics</option>
                    <option value="world" <?= $category === 'world' ? 'selected' : '' ?>>World</option>
                </select>
            </div>
            
            <!-- Submit -->
            <div class="flex items-end">
                <button 
                    type="submit"
                    class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
                >
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</section>

<!-- News Grid -->
<section class="container mx-auto px-4 py-8">
    <?php if (empty($paginatedNews)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No News Found</h3>
            <p class="text-gray-600 dark:text-gray-400">Try adjusting your search or filters</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($paginatedNews as $news): ?>
                <?php
                $imageUrl = $news['img_url'] ?? '';
                if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = STORAGE_URL. $imageUrl;
                }
                ?>
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Image -->
                    <a href="/news/<?= htmlspecialchars($news['slug']) ?>">
                        <div class="relative h-48 overflow-hidden">
                            <?php if ($imageUrl): ?>
                                <img 
                                    src="<?= htmlspecialchars($imageUrl) ?>" 
                                    alt="<?= htmlspecialchars($news['title']) ?>"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-4xl font-bold"><?= substr($news['title'], 0, 1) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($news['is_featured'] ?? false): ?>
                                <div class="absolute top-2 right-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    Featured
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Category & Date -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">
                                <?= htmlspecialchars($news['category']) ?>
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                <?= date('M d, Y', strtotime($news['created_at'])) ?>
                            </span>
                        </div>
                        
                        <!-- Title -->
                        <a href="/news/<?= htmlspecialchars($news['slug']) ?>">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <?= htmlspecialchars($news['title']) ?>
                            </h3>
                        </a>
                        
                        <!-- Tags -->
                        <?php if (!empty($news['tags'])): ?>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach (array_slice(explode(',', $news['tags']), 0, 3) as $tag): ?>
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded">
                                        <?= htmlspecialchars(trim($tag)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Read More -->
                        <a 
                            href="/news/<?= htmlspecialchars($news['slug']) ?>"
                            class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline font-medium"
                        >
                            Read More
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center items-center gap-2 mt-12">
                <?php if ($page > 1): ?>
                    <a 
                        href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . urlencode($category) : '' ?>"
                        class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a 
                        href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . urlencode($category) : '' ?>"
                        class="px-4 py-2 <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?> rounded-lg transition-colors"
                    >
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a 
                        href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . urlencode($category) : '' ?>"
                        class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        Next
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

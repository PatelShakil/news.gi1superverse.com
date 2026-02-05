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

<!-- Premium Hero Section -->
<section class="hero-gradient text-white py-20 lg:py-32 relative">
    <!-- Floating Particles -->
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    
    <!-- Hero Content -->
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Glassmorphism Badge -->
            <div class="inline-flex items-center gap-2 glass-effect rounded-full px-6 py-3 mb-8 fade-in-up">
                <svg class="w-5 h-5 search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="text-sm font-semibold tracking-wide">Breaking News & Latest Updates</span>
            </div>
            
            <!-- Hero Title -->
            <h1 class="hero-title text-5xl md:text-7xl font-extrabold mb-6 leading-tight fade-in-up" style="animation-delay: 0.1s;">
                Discover Stories<br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-yellow-200 via-pink-200 to-blue-200">
                    That Matter
                </span>
            </h1>
            
            <!-- Hero Subtitle -->
            <p class="text-xl md:text-2xl opacity-95 mb-8 leading-relaxed fade-in-up" style="animation-delay: 0.2s;">
                Stay informed with real-time news coverage from around the world.<br class="hidden md:block"/>
                Your trusted source for breaking news and in-depth analysis.
            </p>
            
            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-6 max-w-2xl mx-auto fade-in-up" style="animation-delay: 0.3s;">
                <div class="glass-effect rounded-2xl p-6 hover-glow">
                    <div class="text-3xl md:text-4xl font-bold mb-2"><?= count($newsList) ?>+</div>
                    <div class="text-sm md:text-base opacity-90">Articles</div>
                </div>
                <div class="glass-effect rounded-2xl p-6 hover-glow">
                    <div class="text-3xl md:text-4xl font-bold mb-2">8</div>
                    <div class="text-sm md:text-base opacity-90">Categories</div>
                </div>
                <div class="glass-effect rounded-2xl p-6 hover-glow">
                    <div class="text-3xl md:text-4xl font-bold mb-2">24/7</div>
                    <div class="text-sm md:text-base opacity-90">Coverage</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Wave -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="currentColor" class="text-white dark:text-gray-900"/>
        </svg>
    </div>
</section>

<!-- Premium Search and Filter -->
<section class="container mx-auto px-4 -mt-12 relative z-20">
    <div class="search-container max-w-5xl mx-auto">
        <div class="search-inner p-8">
            <form method="GET" action="/news" id="searchForm">
                <!-- Filter Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Filter News</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Find exactly what you're looking for</p>
                        </div>
                    </div>
                    <?php if ($search || $category): ?>
                        <a href="/news" class="text-sm text-purple-600 dark:text-purple-400 hover:underline font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </a>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <!-- Search Input -->
                    <div class="md:col-span-5 filter-item fade-in-up">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search Articles
                            </span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="<?= htmlspecialchars($search) ?>"
                                placeholder="Type keywords..."
                                class="modern-input w-full px-5 py-3.5 rounded-xl focus:outline-none text-gray-900 dark:text-white placeholder-gray-400"
                            >
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Dropdown -->
                    <div class="md:col-span-4 filter-item fade-in-up">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Category
                            </span>
                        </label>
                        <select 
                            name="category"
                            class="modern-input modern-select w-full px-5 py-3.5 rounded-xl focus:outline-none focus:ring-2 placeholder-gray-400 focus:ring-blue-500 text-gray-900 cursor-pointer"
                        >
                            <option value="">All Categories</option>
                            <option value="technology" <?= $category === 'technology' ? 'selected' : '' ?>>🔧 Technology</option>
                            <option value="business" <?= $category === 'business' ? 'selected' : '' ?>>💼 Business</option>
                            <option value="entertainment" <?= $category === 'entertainment' ? 'selected' : '' ?>>🎬 Entertainment</option>
                            <option value="sports" <?= $category === 'sports' ? 'selected' : '' ?>>⚽ Sports</option>
                            <option value="health" <?= $category === 'health' ? 'selected' : '' ?>>🏥 Health</option>
                            <option value="science" <?= $category === 'science' ? 'selected' : '' ?>>🔬 Science</option>
                            <option value="politics" <?= $category === 'politics' ? 'selected' : '' ?>>🏛️ Politics</option>
                            <option value="world" <?= $category === 'world' ? 'selected' : '' ?>>🌍 World</option>
                        </select>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="md:col-span-3 filter-item fade-in-up">
                        <button 
                            type="submit"
                            class="modern-button w-full px-6 py-3.5 text-white rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-lg"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>Apply Filters</span>
                        </button>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <?php if ($search || $category): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Filters:</span>
                            <?php if ($search): ?>
                                <span class="filter-badge inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-indigo-100 dark:from-purple-900 dark:to-indigo-900 text-purple-700 dark:text-purple-300 rounded-full text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    "<?= htmlspecialchars($search) ?>"
                                </span>
                            <?php endif; ?>
                            <?php if ($category): ?>
                                <span class="filter-badge inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <?= ucfirst(htmlspecialchars($category)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
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
                    $imageUrl = STORAGE_URL . $imageUrl;
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

# Gi1SuperVerse News - Public Site

SEO-optimized news display site built with PHP for `news.gi1superverse.com`

## Features

- 📰 News listing with search and filters
- 📱 Mobile-first responsive design
- 🎨 EditorJS content rendering
- 📊 Analytics tracking
- 🔗 Social sharing with UTM parameters
- 🚀 PWA support (offline reading)
- 🔍 SEO optimized (meta tags, Open Graph, JSON-LD)
- ⚡ Performance optimized (caching, lazy loading)

## Requirements

- PHP 7.4 or higher
- Apache with mod_rewrite enabled
- cURL extension
- JSON extension

## Installation

### 1. Upload Files

Upload all files to your web server (e.g., `/public_html/news/`)

### 2. Configure

Edit `includes/config.php`:

```php
// Update these values
define('API_BASE_URL', 'https://your-api-domain.com/api');
define('SITE_URL', 'https://news.gi1superverse.com');
```

### 3. Set Permissions

```bash
chmod 644 .htaccess
chmod 755 includes/
chmod 755 assets/
```

### 4. Configure DNS

Point subdomain `news.gi1superverse.com` to your server

### 5. Enable SSL

Install SSL certificate for HTTPS (recommended: Let's Encrypt)

## Directory Structure

```
news-site/
├── index.php              # News listing page
├── article.php            # Single news article
├── .htaccess             # URL rewriting rules
├── manifest.json         # PWA manifest
├── sw.js                 # Service worker
├── includes/
│   ├── config.php        # Configuration
│   ├── api-client.php    # API wrapper
│   ├── editorjs-renderer.php
│   ├── meta-tags.php
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/
    │   └── style.css
    ├── js/
    │   ├── main.js
    │   ├── analytics.js
    │   └── pwa.js
    └── images/
        └── icons/        # PWA icons (add 72x72 to 512x512)
```

## URL Structure

- `/` or `/news` - News listing
- `/news/{slug}` - Single news article
- `/category/{category}` - Category filtered news
- `/search?q={query}` - Search results

## SEO Features

### Meta Tags
- Title, description, keywords
- Open Graph tags (Facebook)
- Twitter Card tags
- Canonical URLs

### Structured Data
- JSON-LD NewsArticle schema
- Automatic generation per article

### Performance
- Gzip compression
- Browser caching
- Lazy image loading
- Service worker caching

## Social Sharing

All share buttons include UTM parameters:

```
utm_source={platform}      # facebook, twitter, linkedin, whatsapp
utm_medium=social
utm_campaign=news_share
utm_content={news-slug}
```

Example:
```
https://news.gi1superverse.com/news/article-slug?utm_source=facebook&utm_medium=social&utm_campaign=news_share&utm_content=article-slug
```

## Analytics Tracking

Automatically tracks:
- Page views
- Unique visitors
- Device type (mobile/tablet/desktop)
- Browser and OS
- Geographic location (country, city)
- UTM parameters
- Referrer
- Social shares

## PWA Features

### Offline Reading
- Visited articles cached automatically
- Works without internet connection

### Install as App
- "Add to Home Screen" prompt
- Full-screen app experience
- App icon on device

## Customization

### Styling
Edit `assets/css/style.css` for custom styles

### Colors
Update Tailwind classes in templates or add custom CSS

### Categories
Add/modify categories in:
- `index.php` (filter dropdown)
- `includes/header.php` (navigation)

### Social Media Links
Update in `includes/config.php`:
```php
define('FACEBOOK_URL', 'https://facebook.com/yourpage');
define('TWITTER_URL', 'https://twitter.com/yourhandle');
define('LINKEDIN_URL', 'https://linkedin.com/company/yourcompany');
```

## Troubleshooting

### Clean URLs Not Working

1. Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

2. Check `.htaccess` is readable
3. Verify `AllowOverride All` in Apache config

### Images Not Loading

1. Check API_BASE_URL in config.php
2. Verify CORS headers on API server
3. Check image paths in database

### Analytics Not Tracking

1. Verify API endpoints are accessible
2. Check browser console for errors
3. Ensure cURL is enabled in PHP

### PWA Not Installing

1. Ensure HTTPS is enabled
2. Check manifest.json is accessible
3. Verify service worker registration
4. Add PWA icons (72x72 to 512x512)

## Production Checklist

- [ ] Update API_BASE_URL in config.php
- [ ] Update SITE_URL in config.php
- [ ] Set error_reporting to 0 in config.php
- [ ] Enable HTTPS
- [ ] Configure DNS
- [ ] Add PWA icons
- [ ] Test all pages
- [ ] Test social sharing
- [ ] Verify analytics tracking
- [ ] Submit sitemap to Google
- [ ] Test on mobile devices
- [ ] Test PWA installation

## Support

For issues or questions, contact your development team.

## License

Proprietary - Gi1SuperVerse

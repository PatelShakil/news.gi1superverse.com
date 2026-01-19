// Analytics Tracking Script

function trackAnalytics() {
    // Get news ID from page (set by article.php)
    const newsId = window.newsId;
    const apiBaseUrl = window.apiBaseUrl;

    console.log('Tracking analytics for newsId:', newsId);

    if (!newsId) {
        console.warn('Analytics tracking skipped: newsId is not defined');
        return;
    }

    if (!apiBaseUrl) {
        console.warn('Analytics tracking skipped: apiBaseUrl is not defined');
        return;
    }

    // Collect analytics data
    const analyticsData = {
        ip_address: '', // Will be captured server-side
        user_agent: navigator.userAgent,
        device_type: getDeviceType(),
        browser: getBrowser(),
        operating_system: getOS(),
        source: document.referrer || 'direct',
        session_id: getSessionId(),
        utm_source: getUrlParameter('utm_source'),
        utm_medium: getUrlParameter('utm_medium'),
        utm_campaign: getUrlParameter('utm_campaign'),
        utm_content: getUrlParameter('utm_content'),
    };

    // Send analytics to API
    fetch(`${apiBaseUrl}/news/analytics/${newsId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(analyticsData)
    })
        .then(response => response.json())
        .then(data => {
            console.log('Analytics tracked successfully:', data);
        })
        .catch(error => {
            console.error('Error tracking analytics:', error);
        });
}

function getDeviceType() {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
        return 'tablet';
    }
    if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
        return 'mobile';
    }
    return 'desktop';
}

function getBrowser() {
    const ua = navigator.userAgent;
    let browser = 'Unknown';

    if (ua.indexOf('Firefox') > -1) {
        browser = 'Firefox';
    } else if (ua.indexOf('SamsungBrowser') > -1) {
        browser = 'Samsung Internet';
    } else if (ua.indexOf('Opera') > -1 || ua.indexOf('OPR') > -1) {
        browser = 'Opera';
    } else if (ua.indexOf('Trident') > -1) {
        browser = 'Internet Explorer';
    } else if (ua.indexOf('Edge') > -1) {
        browser = 'Edge';
    } else if (ua.indexOf('Chrome') > -1) {
        browser = 'Chrome';
    } else if (ua.indexOf('Safari') > -1) {
        browser = 'Safari';
    }

    return browser;
}

function getOS() {
    const ua = navigator.userAgent;
    let os = 'Unknown';

    if (ua.indexOf('Win') > -1) os = 'Windows';
    else if (ua.indexOf('Mac') > -1) os = 'MacOS';
    else if (ua.indexOf('Linux') > -1) os = 'Linux';
    else if (ua.indexOf('Android') > -1) os = 'Android';
    else if (ua.indexOf('iOS') > -1) os = 'iOS';

    return os;
}

function getSessionId() {
    let sessionId = sessionStorage.getItem('session_id');
    if (!sessionId) {
        sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem('session_id', sessionId);
    }
    return sessionId;
}

function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

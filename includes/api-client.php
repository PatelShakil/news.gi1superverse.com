<?php
/**
 * API Client - Wrapper functions for Laravel API calls
 */

require_once __DIR__ . '/config.php';

/**
 * Make API request
 */
function makeApiRequest($endpoint, $method = 'GET', $data = null, $headers = []) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
    
    // Set method
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    // Set headers
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    $allHeaders = array_merge($defaultHeaders, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        error_log('API Request Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return json_decode($response, true);
    }
    
    error_log('API Request Failed: HTTP ' . $httpCode . ' - ' . $response);
    return null;
}

/**
 * Get all news with filters
 */
function getAllNews($params = []) {
    $queryString = http_build_query($params);
    $endpoint = '/news/all' . ($queryString ? '?' . $queryString : '');
    return makeApiRequest($endpoint);
}

/**
 * Get single news by slug
 */
function getNewsBySlug($slug) {
    return makeApiRequest('/news/p/' . urlencode($slug));
}

/**
 * Track news analytics
 */
function trackNewsAnalytics($newsId, $analyticsData) {
    return makeApiRequest('/news/analytics/' . $newsId, 'POST', $analyticsData);
}

/**
 * Track news share
 */
function trackNewsShare($newsId, $platform) {
    return makeApiRequest('/news/track-share/' . $newsId, 'POST', ['platform' => $platform]);
}

/**
 * Like news
 */
function likeNews($newsId) {
    return makeApiRequest('/news/like/' . $newsId, 'GET');
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Get user agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * Get referrer
 */
function getReferrer() {
    return $_SERVER['HTTP_REFERER'] ?? 'direct';
}

/**
 * Parse UTM parameters
 */
function getUTMParams() {
    return [
        'utm_source' => $_GET['utm_source'] ?? null,
        'utm_medium' => $_GET['utm_medium'] ?? null,
        'utm_campaign' => $_GET['utm_campaign'] ?? null,
        'utm_content' => $_GET['utm_content'] ?? null,
    ];
}

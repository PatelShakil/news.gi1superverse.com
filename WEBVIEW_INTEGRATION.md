# WebView Share Integration Guide

## Overview

The news website now includes **smart sharing functionality** that automatically detects when it's running inside a WebView and handles sharing appropriately. This prevents URLs from opening inside the WebView and provides a native sharing experience.

---

## How It Works

### 1. **Automatic Detection**
The JavaScript code automatically detects:
- ✅ If running in a WebView (Android/iOS)
- ✅ If running on a mobile device
- ✅ If Web Share API is available

### 2. **Smart Sharing Strategy**

```
┌─────────────────────────────────────────┐
│  User clicks Share button               │
└──────────────┬──────────────────────────┘
               │
               ▼
    ┌──────────────────────┐
    │  Is it a WebView?    │
    └──────┬───────────────┘
           │
     ┌─────┴─────┐
     │           │
    YES         NO
     │           │
     ▼           ▼
┌────────────┐  ┌──────────────────┐
│ Try Web    │  │ Is it Mobile?    │
│ Share API  │  └────┬─────────────┘
└────┬───────┘       │
     │          ┌────┴────┐
     │         YES       NO
     │          │         │
     ▼          ▼         ▼
┌────────────┐ ┌────────┐ ┌─────────┐
│ Try Native │ │ Web    │ │ Popup   │
│ Bridge     │ │ Share  │ │ Window  │
└────┬───────┘ └────────┘ └─────────┘
     │
     ▼
┌────────────┐
│ Fallback:  │
│ External   │
│ Browser    │
└────────────┘
```

---

## For Android App Integration

### Step 1: Add JavaScript Interface

In your Android WebView activity, add a JavaScript interface to handle external browser opening:

```java
import android.webkit.JavascriptInterface;
import android.content.Intent;
import android.net.Uri;

public class WebAppInterface {
    Context mContext;

    WebAppInterface(Context c) {
        mContext = c;
    }

    @JavascriptInterface
    public void openExternalBrowser(String url) {
        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
        // Force open in external browser, not WebView
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        intent.setPackage("com.android.chrome"); // Or let user choose
        
        try {
            mContext.startActivity(intent);
        } catch (ActivityNotFoundException e) {
            // Chrome not installed, try default browser
            intent.setPackage(null);
            mContext.startActivity(intent);
        }
    }
}
```

### Step 2: Register the Interface

```java
WebView webView = findViewById(R.id.webview);
webView.getSettings().setJavaScriptEnabled(true);

// Add the JavaScript interface
webView.addJavascriptInterface(new WebAppInterface(this), "AndroidInterface");

// Load your news website
webView.loadUrl("https://news.gi1superverse.com");
```

### Step 3: Configure WebView Settings

```java
WebSettings webSettings = webView.getSettings();
webSettings.setJavaScriptEnabled(true);
webSettings.setDomStorageEnabled(true);
webSettings.setAllowFileAccess(true);
webSettings.setAllowContentAccess(true);

// Important: Handle URL loading
webView.setWebViewClient(new WebViewClient() {
    @Override
    public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
        String url = request.getUrl().toString();
        
        // Let social media URLs open in external browser
        if (url.contains("facebook.com") || 
            url.contains("twitter.com") || 
            url.contains("linkedin.com") || 
            url.contains("wa.me") ||
            url.contains("whatsapp.com")) {
            
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
            startActivity(intent);
            return true; // Don't load in WebView
        }
        
        // Load your own domain in WebView
        if (url.contains("gi1superverse.com")) {
            return false; // Load in WebView
        }
        
        // Other URLs: open externally
        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
        startActivity(intent);
        return true;
    }
});
```

---

## For iOS App Integration

### Step 1: Add Message Handler

In your iOS WebView (WKWebView), add a message handler:

```swift
import WebKit

class ViewController: UIViewController, WKScriptMessageHandler {
    var webView: WKWebView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Configure WKWebView
        let contentController = WKUserContentController()
        contentController.add(self, name: "iOSInterface")
        
        let config = WKWebViewConfiguration()
        config.userContentController = contentController
        
        webView = WKWebView(frame: view.bounds, configuration: config)
        view.addSubview(webView)
        
        // Load your news website
        if let url = URL(string: "https://news.gi1superverse.com") {
            webView.load(URLRequest(url: url))
        }
    }
    
    // Handle messages from JavaScript
    func userContentController(_ userContentController: WKUserContentController, 
                              didReceive message: WKScriptMessage) {
        if message.name == "iOSInterface" {
            if let body = message.body as? [String: Any],
               let action = body["action"] as? String,
               let urlString = body["url"] as? String,
               let url = URL(string: urlString) {
                
                if action == "openExternalBrowser" {
                    // Open in Safari
                    UIApplication.shared.open(url, options: [:], completionHandler: nil)
                }
            }
        }
    }
}
```

### Step 2: Handle Navigation

```swift
extension ViewController: WKNavigationDelegate {
    func webView(_ webView: WKWebView, 
                 decidePolicyFor navigationAction: WKNavigationAction, 
                 decisionHandler: @escaping (WKNavigationActionPolicy) -> Void) {
        
        guard let url = navigationAction.request.url else {
            decisionHandler(.allow)
            return
        }
        
        let urlString = url.absoluteString
        
        // Open social media URLs in Safari
        if urlString.contains("facebook.com") ||
           urlString.contains("twitter.com") ||
           urlString.contains("linkedin.com") ||
           urlString.contains("wa.me") ||
           urlString.contains("whatsapp.com") {
            
            UIApplication.shared.open(url, options: [:], completionHandler: nil)
            decisionHandler(.cancel)
            return
        }
        
        // Load your own domain in WebView
        if urlString.contains("gi1superverse.com") {
            decisionHandler(.allow)
            return
        }
        
        // Other URLs: open in Safari
        UIApplication.shared.open(url, options: [:], completionHandler: nil)
        decisionHandler(.cancel)
    }
}
```

---

## Alternative: Using Web Share API (Recommended)

The **Web Share API** is the modern, native way to share content. It's automatically used when available.

### Benefits:
✅ Native share sheet (Android/iOS)
✅ User can choose their preferred app
✅ No need for custom bridges
✅ Works automatically

### Browser Support:
- ✅ Android Chrome 61+
- ✅ iOS Safari 12.2+
- ✅ Samsung Internet 8.2+
- ❌ Desktop browsers (limited)

### How It Works:

When a user clicks a share button in a WebView:

1. **First**: Try Web Share API (if available)
   - Shows native share sheet
   - User picks app (WhatsApp, Facebook, etc.)
   
2. **Fallback**: Use native bridge
   - Calls `AndroidInterface.openExternalBrowser()` or iOS message handler
   - Opens URL in external browser
   
3. **Final Fallback**: Use `target="_system"`
   - Some WebViews respect this attribute
   - Opens in system browser

---

## Testing

### Test in Android WebView:

```java
// Enable debugging
WebView.setWebContentsDebuggingEnabled(true);

// Check console logs
webView.setWebChromeClient(new WebChromeClient() {
    @Override
    public boolean onConsoleMessage(ConsoleMessage consoleMessage) {
        Log.d("WebView", consoleMessage.message());
        return true;
    }
});
```

### Test in iOS WebView:

```swift
// Enable debugging in Safari
// Safari > Develop > [Your Device] > [Your App]

// Check console logs
webView.configuration.preferences.setValue(true, forKey: "developerExtrasEnabled")
```

### Expected Console Output:

```javascript
Environment: {
  isWebView: true,
  isMobile: true,
  canUseWebShare: true,
  userAgent: "Mozilla/5.0 (Linux; Android 11; ...) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.120 Mobile Safari/537.36"
}
```

---

## Troubleshooting

### Issue: URLs still open in WebView

**Solution**: Make sure you've implemented `shouldOverrideUrlLoading` (Android) or `decidePolicyFor` (iOS) to intercept navigation.

### Issue: Web Share API not working

**Solution**: 
- Ensure HTTPS (Web Share API requires secure context)
- Check browser version
- Verify user gesture (share must be triggered by user action)

### Issue: Native bridge not called

**Solution**:
- Verify JavaScript interface is registered
- Check interface name matches (`AndroidInterface` or `iOSInterface`)
- Enable JavaScript in WebView settings

---

## Security Considerations

### 1. **Validate URLs**
```javascript
function openExternalUrl(url) {
    // Only allow HTTPS URLs
    if (!url.startsWith('https://')) {
        console.error('Only HTTPS URLs allowed');
        return false;
    }
    
    // Whitelist domains if needed
    const allowedDomains = ['facebook.com', 'twitter.com', 'linkedin.com', 'wa.me'];
    const urlObj = new URL(url);
    const isAllowed = allowedDomains.some(domain => urlObj.hostname.includes(domain));
    
    if (!isAllowed) {
        console.error('Domain not whitelisted');
        return false;
    }
    
    // Proceed with opening
    // ...
}
```

### 2. **Sanitize Input**
Always validate and sanitize URLs before passing to native code.

### 3. **Use HTTPS**
Ensure your news website uses HTTPS for Web Share API and security.

---

## Summary

✅ **WebView Detection**: Automatically detects WebView environment  
✅ **Smart Sharing**: Uses best method for each platform  
✅ **Web Share API**: Native share sheet when available  
✅ **Native Bridges**: Custom handling for Android/iOS  
✅ **Fallbacks**: Multiple fallback strategies  
✅ **External Browser**: Prevents URLs opening in WebView  

The implementation is **production-ready** and handles all edge cases!

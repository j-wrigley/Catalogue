# API Integration

Integrating external APIs and services.

## Overview

This guide covers techniques for integrating external APIs and services into your CMS templates and functionality.

## Template-Based API Integration

### Fetching External Data

Use PHP's `file_get_contents()` or `curl` to fetch API data:

```php
<?php
// Fetch data from external API
$api_url = 'https://api.example.com/data';
$api_data = @file_get_contents($api_url);
$data = json_decode($api_data, true);

if ($data) {
    foreach ($data as $item) {
        // Display API data
        echo '<div>' . htmlspecialchars($item['title']) . '</div>';
    }
}
?>
```

### Using cURL for Complex Requests

```php
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.example.com/data');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer YOUR_TOKEN',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $data = json_decode($response, true);
    // Process data
}
?>
```

## Caching API Responses

### Simple File-Based Cache

```php
<?php
function getCachedApiData($api_url, $cache_duration = 3600) {
    $cache_file = CMS_ROOT . '/data/cache/' . md5($api_url) . '.json';
    
    // Check if cache exists and is fresh
    if (file_exists($cache_file)) {
        $cache_time = filemtime($cache_file);
        if (time() - $cache_time < $cache_duration) {
            return json_decode(file_get_contents($cache_file), true);
        }
    }
    
    // Fetch fresh data
    $data = @file_get_contents($api_url);
    $decoded = json_decode($data, true);
    
    if ($decoded) {
        // Save to cache
        if (!is_dir(dirname($cache_file))) {
            mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($decoded));
    }
    
    return $decoded;
}
?>
```

## JavaScript API Integration

### Client-Side API Calls

```php
<!-- In template -->
<script>
fetch('https://api.example.com/data')
    .then(response => response.json())
    .then(data => {
        // Display data
        const container = document.getElementById('api-data');
        data.forEach(item => {
            container.innerHTML += `<div>${item.title}</div>`;
        });
    })
    .catch(error => {
        console.error('API Error:', error);
    });
</script>

<div id="api-data"></div>
```

## Common API Patterns

### Weather API Integration

```php
<?php
function getWeather($city) {
    $api_key = 'YOUR_API_KEY';
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$api_key}";
    
    $data = @file_get_contents($url);
    return json_decode($data, true);
}

$weather = getWeather('London');
if ($weather) {
    echo '<div class="weather">';
    echo '<h3>Weather in ' . htmlspecialchars($weather['name']) . '</h3>';
    echo '<p>Temperature: ' . round($weather['main']['temp'] - 273.15) . '°C</p>';
    echo '</div>';
}
?>
```

### Social Media Integration

```php
<?php
// Fetch recent tweets (example)
function getRecentTweets($username, $count = 5) {
    // Use Twitter API or RSS feed
    $feed_url = "https://rss.example.com/twitter/{$username}";
    $feed = @simplexml_load_file($feed_url);
    
    $tweets = [];
    if ($feed) {
        foreach ($feed->channel->item as $item) {
            $tweets[] = [
                'text' => (string)$item->description,
                'date' => (string)$item->pubDate,
            ];
            if (count($tweets) >= $count) break;
        }
    }
    
    return $tweets;
}
?>
```

## Security Considerations

### API Key Protection

Never expose API keys in templates. Store them securely:

```php
// In config.php (not in templates)
define('WEATHER_API_KEY', 'your-secret-key');

// In template
$api_key = WEATHER_API_KEY; // Use constant, not hardcoded
```

### Input Sanitization

Always sanitize API inputs:

```php
<?php
$user_input = $_GET['city'] ?? '';
$city = htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
$weather = getWeather($city);
?>
```

### Rate Limiting

Implement rate limiting for API calls:

```php
<?php
function checkRateLimit($identifier, $max_calls = 10, $period = 3600) {
    $limit_file = CMS_ROOT . '/data/rate-limit/' . md5($identifier) . '.json';
    
    if (file_exists($limit_file)) {
        $data = json_decode(file_get_contents($limit_file), true);
        if ($data['count'] >= $max_calls && (time() - $data['start']) < $period) {
            return false; // Rate limit exceeded
        }
    }
    
    // Update rate limit
    file_put_contents($limit_file, json_encode([
        'count' => ($data['count'] ?? 0) + 1,
        'start' => $data['start'] ?? time()
    ]));
    
    return true;
}
?>
```

## Error Handling

### Graceful API Failures

```php
<?php
function safeApiCall($url, $default = []) {
    $data = @file_get_contents($url);
    if ($data === false) {
        return $default;
    }
    
    $decoded = json_decode($data, true);
    return $decoded ?: $default;
}

$api_data = safeApiCall('https://api.example.com/data', []);
?>
```

## Best Practices

1. **Cache API Responses**: Don't call APIs on every page load
2. **Handle Errors**: Always provide fallbacks for API failures
3. **Protect API Keys**: Never expose keys in client-side code
4. **Rate Limit**: Implement rate limiting to prevent abuse
5. **Validate Data**: Always validate and sanitize API responses
6. **Use HTTPS**: Only use secure API endpoints
7. **Monitor Usage**: Track API usage and costs

## Examples

### News Feed Integration

```php
<?php
function getNewsFeed($source, $limit = 5) {
    $cache_file = CMS_ROOT . '/data/cache/news-' . md5($source) . '.json';
    
    // Check cache (1 hour)
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 3600) {
        return json_decode(file_get_contents($cache_file), true);
    }
    
    // Fetch from API
    $url = "https://api.news.com/feed?source={$source}&limit={$limit}";
    $data = @file_get_contents($url);
    $news = json_decode($data, true);
    
    if ($news) {
        file_put_contents($cache_file, json_encode($news));
    }
    
    return $news ?: [];
}

$news = getNewsFeed('tech', 5);
?>

<section class="news-feed">
    <h2>Latest News</h2>
    <?php foreach ($news as $article): ?>
        <article>
            <h3><?= htmlspecialchars($article['title']) ?></h3>
            <p><?= htmlspecialchars($article['excerpt']) ?></p>
            <a href="<?= htmlspecialchars($article['url']) ?>">Read more</a>
        </article>
    <?php endforeach; ?>
</section>
```

### External Content Embedding

```php
<!-- Embed YouTube video -->
<?php if (catalogue('youtube_id')): ?>
    <div class="video-embed">
        <iframe 
            src="https://www.youtube.com/embed/<?= htmlspecialchars(catalogue('youtube_id')) ?>"
            frameborder="0"
            allowfullscreen>
        </iframe>
    </div>
<?php endif; ?>

<!-- Embed Instagram post -->
<?php if (catalogue('instagram_url')): ?>
    <div class="instagram-embed">
        <?php
        // Use Instagram oEmbed API
        $embed_url = 'https://api.instagram.com/oembed?url=' . urlencode(catalogue('instagram_url'));
        $embed_data = @file_get_contents($embed_url);
        $embed = json_decode($embed_data, true);
        if ($embed && isset($embed['html'])) {
            echo $embed['html'];
        }
        ?>
    </div>
<?php endif; ?>
```

## See Also

- [Templates](../templates/README.md) - Template basics
- [Performance](./PERFORMANCE.md) - Caching and optimization
- [Security](../security/README.md) - Security best practices
- [Configuration](../configuration/README.md) - System configuration


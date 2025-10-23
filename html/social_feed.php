<?php
class SocialFeed {
    private $instagramToken = 'YOUR_INSTAGRAM_ACCESS_TOKEN';
    private $youtubeApiKey = 'YOUR_YOUTUBE_API_KEY';
    private $youtubeChannelId = 'YOUR_YOUTUBE_CHANNEL_ID';
    private $maxPosts = 5;

    public function getInstagramFeed() {
        $url = "https://graph.instagram.com/me/media?fields=id,caption,media_url,permalink,timestamp&access_token={$this->instagramToken}&limit={$this->maxPosts}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true)['data'] ?? [];
    }

    public function getYouTubeVideos() {
        $url = "https://www.googleapis.com/youtube/v3/search?key={$this->youtubeApiKey}&channelId={$this->youtubeChannelId}&part=snippet,id&order=date&maxResults={$this->maxPosts}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true)['items'] ?? [];
    }

    public function formatTimeAgo($date) {
        $time = strtotime($date);
        $timeDiff = time() - $time;
        
        if ($timeDiff < 60) {
            return 'just now';
        } elseif ($timeDiff < 3600) {
            $mins = floor($timeDiff / 60);
            return "$mins minute" . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($timeDiff < 86400) {
            $hours = floor($timeDiff / 3600);
            return "$hours hour" . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            return date('F d, Y', $time);
        }
    }

    public function displayFeed() {
        $instagramPosts = $this->getInstagramFeed();
        $youtubeVideos = $this->getYouTubeVideos();
        
        $allPosts = [];
        
        // Process Instagram posts
        foreach ($instagramPosts as $post) {
            $allPosts[] = [
                'type' => 'instagram',
                'username' => 'zanvarsity',
                'content' => $post['caption'] ?? '',
                'media' => $post['media_url'],
                'link' => $post['permalink'],
                'time' => $this->formatTimeAgo($post['timestamp'])
            ];
        }
        
        // Process YouTube videos
        foreach ($youtubeVideos as $video) {
            $allPosts[] = [
                'type' => 'youtube',
                'username' => 'zanvarsity',
                'content' => $video['snippet']['title'],
                'media' => $video['snippet']['thumbnails']['high']['url'],
                'link' => 'https://www.youtube.com/watch?v=' . $video['id']['videoId'],
                'time' => $this->formatTimeAgo($video['snippet']['publishedAt'])
            ];
        }
        
        // Sort all posts by time (newest first)
        usort($allPosts, function($a, $b) {
            return strtotime($a['time']) < strtotime($b['time']) ? 1 : -1;
        });
        
        return $allPosts;
    }
}
?>

<?php
require_once 'social_feed.php';
$socialFeed = new SocialFeed();
$posts = $socialFeed->displayFeed();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zanvarsity Social Feed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f8fa;
            color: #14171a;
        }
        .post {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .platform-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .username {
            font-weight: bold;
            margin-right: 10px;
            color: #1da1f2;
        }
        .post-time {
            color: #657786;
            font-size: 0.9em;
        }
        .post-content {
            margin: 10px 0;
            line-height: 1.5;
        }
        .post-media {
            max-width: 100%;
            border-radius: 8px;
            margin: 10px 0;
        }
        .post-link {
            color: #1da1f2;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .post-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Zanvarsity Social Feed</h1>
    
    <?php if (empty($posts)): ?>
        <p>No posts found. Please check your API credentials.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <img src="<?php echo $post['type'] === 'instagram' ? 'instagram-icon.png' : 'youtube-icon.png'; ?>" 
                         alt="<?php echo ucfirst($post['type']); ?>" class="platform-icon">
                    <span class="username">@<?php echo htmlspecialchars($post['username']); ?></span>
                    <span class="post-time"><?php echo htmlspecialchars($post['time']); ?></span>
                </div>
                
                <?php if (!empty($post['content'])): ?>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($post['media'])): ?>
                    <div class="post-media">
                        <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($post['media']); ?>" 
                                 alt="Post media" 
                                 style="max-width: 100%; border-radius: 8px;">
                        </a>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo htmlspecialchars($post['link']); ?>" 
                   class="post-link" 
                   target="_blank">
                    View on <?php echo ucfirst($post['type']); ?>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Auto-refresh the feed every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>

# analytics
Fetches Analytics channel data based on oAuth

Example on use

```php
    $youtubeUser = YoutubeUser::find(
          // Provide refresh token
    );
    $channels = $youtubeUser->getChannels();

    $channels
        ->first()
        ->getSubscribers()
```

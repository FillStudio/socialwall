# Social Wall
A simple PHP Class to retrieve public stream from Social Networks.

Every single Social Network stream is implemented by an Adapter of a simple Interface that loads the Posts using its own API.

### Version
1.0.0

### Available Social Networks
  - Facebook
  - Twitter
  - Youtube
  - Instagram

### Results
The Class will return a key => value Array ordered by post date.

### External Libs
Social Wall uses official external SDK Libs for:
  - [Facebook](https://github.com/facebook/facebook-php-sdk-v4)
  - [Twitter](https://twitteroauth.com/)
  - [Youtube](https://github.com/google/google-api-php-client)

### Example
SocialWall is very easy to use:

 * Include the library
```sh
require_once('SocialWall.php');
```

 * Setup your configuration
```sh
$limit = 10;
$social_config = array(
    'facebook'=>array(
		'id'=>'your_facebook_id',
        'limit'=>$limit,
        'api_key' => 'your_api_key',
        'api_secret'=> 'your_api_secret',
        'app_token'=> 'your_app_token'
    ),
    'twitter'=>array(
		'id'=>'your_twitter_username',
        'limit'=>$limit,
        'media_size' => 'medium',
        'access_token' => 'your_access_token',
        'access_token_secret' => 'your_access_token_secret',
        'consumer_key' => 'your_consumer_key',
        'consumer_secret' => 'your_consumer_secret'
    ),
    'youtube'=>array(
		'id'=>'your_youtube_id',
        'limit'=>$limit,
        'api_key' => 'your_api_key'
    ),
    'instagram' => array(
		'id'=>'your_instagram_id',
        'limit'=>$limit,
        'client_id' => 'your_client_id',
        'client_secret' => 'your_client_secret'
    )
);
```

 * Then init the class
```sh
$socialwall = new SocialWall();
$socialwall->setLogEmail("debug@yoursite.com");
```

 * And that's all! Now you can call the "load" method
```sh
 $posts = $socialwall->load($social_config);
```

## License
Feel free to fork this project and contribute implementing others Adapters.

## Authors
Made with ♥ in [FillStudio](http://www.fillstudio.com) by [Aldo Zorzi](https://github.com/aldozorzi) & [Emanuele Tortolone](https://github.com/emanueletortolone).

<?php

/* EXAMPLE

{
  "username": "Webhook",
  "avatar_url": "https://i.imgur.com/4M34hi2.png",
  "content": "Text message. Up to 2000 characters.",
  "embeds": [
    {
      "author": {
        "name": "Birdie♫",
        "url": "https://www.reddit.com/r/cats/",
        "icon_url": "https://i.imgur.com/R66g1Pe.jpg"
      },
      "title": "Title",
      "url": "https://google.com/",
      "description": "Text message. You can use Markdown here. *Italic* **bold** __underline__ ~~strikeout~~ [hyperlink](https://google.com) `code`",
      "color": 15258703,
      "fields": [
        {
          "name": "Text",
          "value": "More text",
          "inline": true
        },
        {
          "name": "Even more text",
          "value": "Yup",
          "inline": true
        },
        {
          "name": "Use `\"inline\": true` parameter, if you want to display fields in the same line.",
          "value": "okay..."
        },
        {
          "name": "Thanks!",
          "value": "You're welcome :wink:"
        }
      ],
      "thumbnail": {
        "url": "https://upload.wikimedia.org/wikipedia/commons/3/38/4-Nature-Wallpapers-2014-1_ukaavUI.jpg"
      },
      "image": {
        "url": "https://upload.wikimedia.org/wikipedia/commons/5/5a/A_picture_from_China_every_day_108.jpg"
      },
      "footer": {
        "text": "Woah! So cool! :smirk:",
        "icon_url": "https://i.imgur.com/fKL31aD.jpg"
      }
    }
  ]
}

*/

/**
 * 
 */
class DiscordWebhookPayloadCreator {
	
	public $user_id;
	public $user_name;
	public $game_title;
	public $stream_title;

	public function __construct(array $payload, string $game_title) {
		$this->user_id = $payload['data'][0]['user_id'];
        $this->user_name = $payload['data'][0]['user_name'];
        $this->game_title = $game_title;
        $this->stream_title = $payload['data'][0]['title']; 
	}

	public function formatPayload() {
		$formatted_payload = 
			["embeds" => [
				[
				"title" => "New Stream Alert!",
				"description" => "Holy shit, {$this->user_name} is streaming RIGHT NOW!",
				"color" => 15258703,
				"fields" => [
					[
						"name" => "Game",
						"value" => "{$this->game_title}",
						"inline" => true
					],
					[
						"name" => "Sponsor",
						"value" => "<:blowpop:713971667604471821>",
						"inline" => true					
					],
					[
						"name" => "Stream",
						"value" => "To follow along with {$this->user_name}'s shenanigans, [click here](https://www.twitch.tv/{$this->user_name}) <:showmewhatyougot:712480575843205220>",
					]
				],
				"image" => [
					"url" => "https://media.giphy.com/media/RwLDkna2fN3fG/giphy.gif"
				],
				"footer" => [
					"text" => "No matter what the above says, we will always be sponsored by Burger King's flame broiled Rodeo Cheeseburger.",
					"icon_url" => "https://assets3.thrillist.com/v1/image/2852867/792x528/crop;jpeg_quality=60;progressive.jpg"
				]
			]
		]
	];

	return $formatted_payload;
	}

}


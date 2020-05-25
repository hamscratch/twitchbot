<?php

/**
 * 
 */
class DiscordWebhookPayloadCreator {
	const GIFS = [
		'https://media.giphy.com/media/5bvKQc6PLnqvhG0Bxj/giphy.gif', // nic cage
		'https://media.giphy.com/media/RwLDkna2fN3fG/giphy.gif', // homer and bart on couch
		'https://giphy.com/gifs/cat-national-soup-WJK2SABYwvEvm/giphy.gif', // cat wat
		'https://media.giphy.com/media/l41YxszG6LI9jx69O/giphy.gif', // welcome thrillho
		'https://media.giphy.com/media/127LCkdUYpgSgU/giphy.gif', // milhouse bonestorm
		'https://media.giphy.com/media/Q7hGxSxIalZyo/giphy.gif', // coyote moon
		'https://media.giphy.com/media/citBl9yPwnUOs/giphy.gif', // where's the any key?
		'https://media.giphy.com/media/3o6Mb4d6BuhssF2OE8/giphy.gif', // if there's a better use for the internet
		'https://media.giphy.com/media/OMK7LRBedcnhm/giphy.gif', // nerdddd
		'https://media.giphy.com/media/xT5LMVErntn8tBWMbS/giphy.gif', // moe's family feed bag
		'https://media.giphy.com/media/3oKIPwoeGErMmaI43S/giphy.gif', // mascot running from explosions
		'https://media.giphy.com/media/l0MYGb1LuZ3n7dRnO/giphy.gif', // die hard welcome
		'https://media.giphy.com/media/fqVQbYttUbZMQbHkQJ/giphy.gif', // the nightman cometh
		'https://media.giphy.com/media/sT4S8nuzENtAY/giphy.gif', // assemble your crew
		'https://media.giphy.com/media/B4jfJqiIxvU08/giphy.gif', // BTAS
		'https://media.giphy.com/media/mxDZecDOOsWCA/giphy.gif', // so it begins
		'https://media.giphy.com/media/l0HlPtbGpcnqa0fja/giphy.gif', // randy streaming
		'https://media.giphy.com/media/l2Sqg1iEWObH3oz2E/giphy.gif', // power streamer
		'https://media.giphy.com/media/l2Je733DgHfXIWEve/giphy.gif', // alf pogs
		'https://media.giphy.com/media/SFp9RLto964Ew/giphy.gif', // poochie
		'https://media.giphy.com/media/DyM4NUSmuJV3G/giphy.gif', // mr sparkle
	];

	const SPONSORS = [
		"This stream is brought to you by our currenct sponsor, Charms Blowpops. Did you know that during World War II, the U.S. Army began including Charms candies in combat rations as a supplemental energy form. That tradition has continued with a few interruptions. - [Source](https://en.wikipedia.org/wiki/Charms_Blow_Pops)",
		"This stream is brought to you by our currenct sponsor, the classic fan favorite, none other than the flame broiled Rodeo Cheeseburger from Burger King. Our new Rodeo Burger features a savory flame-grilled beef patty topped with sweet and smoky BBQ sauce and crispy, golden onion rings served on a toasted, sesame seed bun.",
	];

	public $user_id;
	public $user_name;
	public $game_title;
	public $stream_title;
	public $gif_picker;

	public function __construct(array $payload, string $game_title) {
		$this->user_id = $payload['data'][0]['user_id'];
        $this->user_name = $payload['data'][0]['user_name'];
        $this->game_title = $game_title;
        $this->stream_title = $payload['data'][0]['title']; 
	}

	public function formatPayload() {
		$gif = $this->pickGif();
		$sponsor_text = $this->pickSponsor();

		$formatted_payload = 
			["embeds" => [
				[
				"title" => "{$this->user_name} is now streaming!",
				"description" => "{$sponsor_text}",
				"color" => 0x8f00ff,
				"fields" => [
					[
						"name" => "Game",
						"value" => "{$this->game_title}",
						"inline" => true
					],
					[
						"name" => "Title",
						"value" => "{$this->stream_title}",
						"inline" => true					
					],
					[
						"name" => "Stream",
						"value" => "[Click here](https://www.twitch.tv/{$this->user_name}) to follow along with {$this->user_name}'s shenanigans. <:showmewhatyougot:712480575843205220>",
					]
				],
				"image" => [
					"url" => "{$gif}"
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

	public function pickGif() : string {
		$gif = array_rand(array_flip(self::GIFS), 1);

		return $gif;
	}

	public function pickSponsor() : string {
		$sponsor = array_rand(array_flip(self::SPONSORS), 1);

		return $sponsor;
	}

}


<?php


/*
Nic cage con air
https://media.giphy.com/media/5bvKQc6PLnqvhG0Bxj/giphy.gif

come sit with us
https://media.giphy.com/media/RwLDkna2fN3fG/giphy.gif

cat wat
https://giphy.com/gifs/cat-national-soup-WJK2SABYwvEvm/giphy.gif




BK Rodeo Cheeseburger image
https://assets3.thrillist.com/v1/image/2852867/792x528/crop;jpeg_quality=60;progressive.jpg

*/

class GifPicker {
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

	public function pickGif() : string {
		$gif = array_rand(array_flip(self::GIFS), 1);

		return $gif;
	}

}
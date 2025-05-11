<?php
	/*
	* PHP Embed Class
	* This will just embed Video, Images, Audio according to the providers
	* Supports: Dailymotion, Youtube, Vimeo, Soundcloud, Slideshare, Facebook, Giphy
	* Paulo Regina - Mar 2024
	* Version: 1.4
	*/
	class EmbedEmbed 
	{
		// Transforms URLs into both clickable links and embeddable iframes, also appending https:// to URLs that start with www.
		public function oembed($message) 
		{
			$urls = $this->extractUrl($message);
			if(!empty($urls)) 
			{
				$embedCodes = array();
				foreach($urls as $url) 
				{
					$embedCodes[] =  $this->getEmbedCode($url);
				}
				$embedCode = '';
				foreach($embedCodes as $iframes)
				{
					$embedCode .= $iframes;
				}

				$regex = '/\b(?:https?:\/\/|www\.)\S+\b/';
				$www_regex = '/^www\./m';
				preg_match_all($regex, $message, $matches, PREG_SET_ORDER);
				foreach($matches as $match) 
				{
					if(preg_match($www_regex, $match[0])) 
					{
						$www_link = 'https://' . $match[0];
						$message = preg_replace('/(?<!\S)' . preg_quote($match[0], '/') . '(?!\S)/', '<a href="' . $www_link . '" target="_blank">' . $match[0] . '</a>', $message);
					} else {
						$message = preg_replace('/(?<!\S)' . preg_quote($match[0], '/') . '(?!\S)/', '<a href="' . $match[0] . '" target="_blank">' . $match[0] . '</a>', $message);
					}
				}
				
				$messageFin = $message . '<br />' . $embedCode;

				return $messageFin;
			} else {
				return $message;
			}
		}
		
		// Extract Url from the text
		public function extractUrl($text) 
		{
			$regex = '/(https?:\/\/[^\s]+)/';
			preg_match_all($regex, $text, $matches);
			return isset($matches[0]) ? $matches[0] : null;
		}
		
		// Get Embed Code
		public function getEmbedCode($url) 
		{
			if ($this->isDailymotion($url)) {
				return $this->getDailymotionEmbed($url);
			} elseif ($this->isYoutube($url)) {
				return $this->getYoutubeEmbed($url);
			} elseif ($this->isVimeo($url)) {
				return $this->getVimeoEmbed($url);
			} elseif ($this->isSoundCloud($url)) {
				return $this->getSoundCloudEmbed($url);
			} elseif ($this->isSlideShare($url)) {
				return $this->getSlideShareEmbed($url);
			} elseif ($this->isFacebookWatch($url)) {
				$reelUrl = $this->convertToReelUrl($url);
				return $this->getFacebookWatchEmbed($reelUrl);
			}  elseif ($this->isGiphy($url)) {
				return $this->getGiphyEmbed($url);
			} else {
				return '';
			}
		}
		
		private function isDailymotion($url) 
		{
			return (strpos($url, 'dailymotion.com') !== false);
		}
		
		private function getDailymotionEmbed($url) 
		{
			$videoId = basename(parse_url($url, PHP_URL_PATH));
			return "<iframe frameborder=\"0\" width=\"100%\" height=\"270\" src=\"https://www.dailymotion.com/embed/video/$videoId\" allowfullscreen></iframe>";
		}
		
		private function isYoutube($url) 
		{
			return (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false);
		}
		
		private function getYoutubeEmbed($url) 
		{
			$videoId = $this->getYoutubeVideoId($url);
			return "<iframe width=\"100%\" height=\"315\" src=\"https://www.youtube.com/embed/$videoId\" frameborder=\"0\" allowfullscreen></iframe>";
		}
		
		private function getYoutubeVideoId($url) 
		{
			parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
			return isset($queryParams['v']) ? $queryParams['v'] : basename(parse_url($url, PHP_URL_PATH));
		}
		
		private function isVimeo($url) 
		{
			return (strpos($url, 'vimeo.com') !== false);
		}
		
		private function getVimeoEmbed($url) 
		{
			$videoId = basename(parse_url($url, PHP_URL_PATH));
			return "<iframe src=\"https://player.vimeo.com/video/$videoId\" width=\"100%\" height=\"220\" frameborder=\"0\" allowfullscreen></iframe>";
		}
		
		private function isSoundCloud($url) 
		{
			return (strpos($url, 'soundcloud.com') !== false);
		}
		
		private function getSoundCloudEmbed($url) 
		{
			$getValues = file_get_contents('https://soundcloud.com/oembed?format=js&url='.$url.'&iframe=true');
			$decodeiFrame=substr($getValues, 1, -2);
			$jsonObj = json_decode($decodeiFrame);
			return $jsonObj->html;
		}
		
		private function isSlideShare($url) 
		{
			return (strpos($url, 'slideshare.net') !== false);
		}
		
		private function getSlideShareEmbed($url) 
		{
			$json = file_get_contents("https://www.slideshare.net/api/oembed/2?url=" . urlencode($url) . "&format=json");
			$data = json_decode($json);
			return $data->html;
		}
		
		private function isFacebookWatch($url) 
		{
			return (strpos($url, 'facebook.com') !== false && strpos($url, '/videos/') !== false);
		}
		
		private function convertToReelUrl($url) 
		{
			$parts = explode('/videos/', $url);
			if (count($parts) === 2) {
				$userId = basename($parts[0]);
				$videoId = basename($parts[1]);
				return "https://www.facebook.com/reel/$videoId/";
			}
			return $url;
		}
		
		private function getFacebookWatchEmbed($url) 
		{
			$url = rtrim($url, '/');
			$url = urlencode($url);
			return "<iframe src=\"https://www.facebook.com/plugins/video.php?height=476&width=560&href=$url\" width=\"100%\" height=\"320\" style=\"border:none;overflow:hidden\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" allowFullScreen=\"true\"></iframe>";
		}
		
		private function isGiphy($url) 
		{
			return (strpos($url, 'giphy.com') !== false);
		}
		
		private function getGiphyEmbed($url) 
		{
			$parts = explode('-', rtrim($url, '/'));
			$gifId = end($parts);
			
			$embedUrl = "https://giphy.com/embed/$gifId";
			
			return "<iframe src=\"$embedUrl\" width=\"100%\" height=\"270\" frameBorder=\"0\" class=\"giphy-embed\" allowFullScreen></iframe>";
		}
	}
	
	$embed = new EmbedEmbed();
	
?>
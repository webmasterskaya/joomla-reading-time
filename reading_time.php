<?php
/**
 * @package    Joomla - Reading time
 * @version    __DEPLOY_VERSION__
 * @author     Artem Vasilev - webmasterskaya.xyz
 * @copyright  Copyright (c) 2018 - 2020 Webmasterskaya. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://webmasterskaya.xyz/
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die;

class PlgContentReading_Time extends CMSPlugin
{
	/**
	 * The Application object
	 *
	 * @var    JApplicationSite
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * The supported form contexts
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $supportedContext = [
		'com_content.article'
	];

	protected $readingTime;

	/**
	 * @param   string    $context     The context of the content being passed to the plugin.
	 * @param   object   &$article     The article object.  Note $article->text is also available
	 * @param   mixed    &$params      The article params
	 * @param   integer   $limitstart  The 'page' number
	 *
	 * @since 1.0.0
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		if (!$this->checkSupport($context) || empty($article->text))
		{
			return;
		}

		// Loading styles and scripts
		HTMLHelper::_('script', 'plg_content_reading_time/script.js', ['relative' => true, 'version' => 'auto']);
		HTMLHelper::_('stylesheet', 'plg_content_reading_time/style.css', ['relative' => true, 'version' => 'auto']);

		// Set the plugin parameters for the script
		$document = Factory::getDocument();
		$document->addScriptOptions('plg_content_reading_time', [
			'progressbar'          => $this->params->get('progressbar', 1),
			'progressbar_position' => $this->params->get('progressbar_position', 1),
		], true);

		$this->readingTime     = $this->getReadingTime($article->text);
		$article->reading_time = $this->readingTime;
	}

	/**
	 * @param   string  $context
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function checkSupport($context)
	{
		if ($this->app->isClient('site') && in_array($context, $this->supportedContext))
		{
			return true;
		}

		return false;
	}

	/**
	 * Calculation of time for acquaintance with content
	 *
	 * @param   string  $content
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getReadingTime($content)
	{
		preg_match_all("~<img~i", $content, $resImgs);
		$imagesTime = count($resImgs[0]) * intval($this->params->get('sec_on_image', 5));
		$words      = count(explode(' ', strip_tags(str_replace(["\n", '><'], ' ', $content))));
		$fullTime   = ($words / intval($this->params->get('words_pear_min', 200))) * 60 + $imagesTime;

		$result = new \stdClass();

		if (!empty($fullTime))
		{
			$result->fullTime = $fullTime;
			if ($this->params->get('time_format', 1) == 1)
			{
				$minutes          = floor($fullTime / 60);
				$seconds          = round(($fullTime % 60) / 10) * 10;
				$result->formated = trim(($minutes ? $minutes . ' ' . Text::_('PLG_CONTENT_READING_TIME_MINUTES') : '') . ($seconds ? $seconds . ' ' . Text::_('PLG_CONTENT_READING_TIME_SECONDS') : ''));
			}
			else
			{
				$result->formated = trim(($fullTime ? $fullTime . ' ' . Text::_('PLG_CONTENT_READING_TIME_SECONDS') : ''));
			}
		}

		return $result;
	}

	/**
	 * @param   string    $context     The context of the content being passed to the plugin.
	 * @param   object   &$article     The article object.  Note $article->text is also available
	 * @param   mixed    &$params      The article params
	 * @param   integer   $limitstart  The 'page' number
	 *
	 * @return string|void
	 *
	 * @since 1.0.0
	 */
	public function onContentAfterTitle($context, &$article, &$params, $limitstart)
	{
		if (!$this->checkSupport($context) || empty($article->text))
		{
			return;
		}

		$output = [];

		if ($this->params->get('time_position', '1') == '1' && !empty($this->readingTime))
		{
			$output[] = $this->displayReadingTime();
		}

		return implode(PHP_EOL, $output);
	}

	/**
	 * Displays a block with time
	 *
	 * @return false|string
	 *
	 * @since 1.0.0
	 */
	protected function displayReadingTime()
	{
		$readingTime = $this->readingTime;
		$path        = JPluginHelper::getLayoutPath('content', 'reading_time', 'time-block');
		ob_start();
		include $path;
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * @param   string    $context     The context of the content being passed to the plugin.
	 * @param   object   &$article     The article object.  Note $article->text is also available
	 * @param   mixed    &$params      The article params
	 * @param   integer   $limitstart  The 'page' number
	 *
	 * @return string|void
	 *
	 * @since 1.0.0
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
	{
		if (!$this->checkSupport($context) || empty($article->text))
		{
			return;
		}

		$output = [];

		if ($this->params->get('time_position', '1') == '2' && !empty($this->readingTime))
		{
			$output[] = $this->displayReadingTime();
		}

		// Show mark for calculating progress
		if ($this->params->get('progressbar', 1))
		{
			$output[] = '<span id="reading-time-marker-start"></span>';
		}

		return implode(PHP_EOL, $output);
	}

	/**
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param $limitstart
	 *
	 * @return string|void
	 *
	 * @since 1.0.0
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
	{
		if (!$this->checkSupport($context) || empty($article->text))
		{
			return;
		}

		$output = [];

		if ($this->params->get('time_position', '1') == '3' && !empty($this->readingTime))
		{
			$output[] = $this->displayReadingTime();
		}

		// Show mark for calculating progress
		if ($this->params->get('progressbar', 1))
		{
			$output[] = '<span id="reading-time-marker-end"></span>';
		}

		return implode(PHP_EOL, $output);
	}
}
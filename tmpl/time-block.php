<?php
/**
 * @package    Joomla - Reading time
 * @version    1.0.0
 * @author     Artem Vasilev - webmasterskaya.xyz
 * @copyright  Copyright (c) 2018 - 2020 Webmasterskaya. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://webmasterskaya.xyz/
 */

use Joomla\CMS\Language\Text;

/** @var stdClass $readingTime */

defined('_JEXEC') or die;
?>
<div id="reading-time-block" class="reading-time-block muted">
    <span class="icon-clock"></span>
    <span><?php echo Text::sprintf('PLG_CONTENT_READING_TIME_READ_TIME', $readingTime->formated); ?></span>
</div>
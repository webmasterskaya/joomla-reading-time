/**
 * @package    Joomla - Reading time
 * @version    1.1.0
 * @author     Artem Vasilev - webmasterskaya.xyz
 * @copyright  Copyright (c) 2018 - 2020 Webmasterskaya. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://webmasterskaya.xyz/
 */

document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla !== 'undefined') {
		var readingTime = {
			'options': Joomla.getOptions('plg_content_reading_time'),
			'progressbar': document.getElementById('reading-time-progress'),
		};

		if (!!readingTime.progressbar
			&& readingTime.options.progressbar === '1') {
			var progressbarInner = readingTime.progressbar.getElementsByTagName(
				'span')[0];
			readingTime.progressbar.style.height = (readingTime.options.progressbar_height
				? readingTime.options.progressbar_height
				: 10) + 'px';
			readingTime.progressbar.style.background = (readingTime.options.progressbar_color_bg
				? readingTime.options.progressbar_color_bg
				: '#555');
			progressbarInner.style.background = (readingTime.options.progressbar_color_active
				? readingTime.options.progressbar_color_active
				: '#4bdd5b');
			readingTime.progressbar.style.display = 'block';
			if (readingTime.options.progressbar_position === '1') {
				readingTime.progressbar.style.top = 0;
				readingTime.progressbar.style.bottom = 'auto';
				document.body.style.marginTop = (readingTime.options.progressbar_height
					? readingTime.options.progressbar_height
					: 10) + 'px';
			}
			else {
				readingTime.progressbar.style.top = 'auto';
				readingTime.progressbar.style.bottom = 0;
				document.body.style.marginBottom = (readingTime.options.progressbar_height
					? readingTime.options.progressbar_height
					: 10) + 'px';
			}

			var height = Math.max(
				document.body.scrollHeight,
				document.body.offsetHeight,
				document.documentElement.clientHeight,
				document.documentElement.scrollHeight,
				document.documentElement.offsetHeight,
			);

			var setProgress = function() {
				var scrollFromTop = (document.documentElement.scrollTop ||
					document.body.scrollTop);
				var width = (scrollFromTop /
					(height - document.documentElement.clientHeight)) * 100 +
					'%';

				readingTime.progressbar.getElementsByTagName(
					'span')[0].style.width = width;
			};

			window.addEventListener('scroll', setProgress);

			setProgress();
		}
	}
});

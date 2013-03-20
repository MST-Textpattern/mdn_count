//<?php

function mdn_count($atts, $thing=NULL) {
	extract(lAtts(array(
		'section' => '',
		'category' => '',
		'status' => '4',
		'time' => 'now'
	), $atts));

	$q[] = 'select count(*) count from '.safe_pfx('textpattern').' where 1 = 1';

	if (!empty($section)) {
		$andor = 'and (';
		foreach(explode(',', $section) as $section_split) {
			$section_split = trim($section_split);
			$q[] = $andor.' section = \''.$section_split.'\'';
			$andor = 'or';
		}
		$q[] = ')';
	}

	if (!empty($category)) {
		$andor = 'and (';
		foreach(explode(',', $category) as $category_split) {
			$category_split = trim($category_split);
			$q[] = $andor.' category1 = \''.$category_split.'\' or category2 = \''.$category_split.'\'';
			$andor = 'or';
		}
		$q[] = ')';
	}

	$andor = 'and (';
	foreach (explode(',', $status) as $status_split) {
		$status_split = trim($status_split);
		if (is_numeric($status_split) && ($status_split < 6) && ($status_split > 0)) {
			$q[] = $andor.' status = \''.$status_split.'\'';
			$andor = 'or';
		}
	}
	if ($andor != 'and (') {
		$q[] = ')';
	}

	$andor = 'and (';
	foreach (explode(',', $time) as $time_split) {
		switch (trim($time_split)) {
			case 'now':
				$q[] = $andor.' (Posted <= now() and (now() <= Expires or Expires = '.NULLDATETIME.'))';
				$andor = 'or';
				break;
			case 'past':
				$q[] = $andor.' (now() >= Expires)';
				$andor = 'or';
				break;
			case 'future':
				$q[] = $andor.' (Posted >= now())';
				$andor = 'or';
				break;
		}
	}
	if ($andor != 'and (') {
		$q[] = ')';
	}

	$r = getRows(join(' ', $q));
	return $r[0]['count'];
}
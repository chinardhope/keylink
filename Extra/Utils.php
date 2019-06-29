<?php

namespace Keylink\Extra;

class Utils {
	//去重关键词
	public static function mergeUniqueResult($data = [], $existCount = 1) {
		$result = $strLenMap = $posMap = [];
		if (empty($data)) {
			return $result;
		}
		foreach ($data as $row) {
			list($pos, $str) = $row;
			$strLen = mb_strlen($str);
			isset($posMap[$pos]) || $posMap[$pos] = [];
			$posMap[$pos][$strLen] = $str;
		}
		$posArr = array_keys($posMap);
		$nextMinPos = $i = 0;
		$lastStr = '';
		while($i < count($posArr)) {
			$tmPos = $posArr[$i];
			$i++;
			$posRow = $posMap[$tmPos];
			krsort($posRow);
			$tmPosRow = self::array_key_value_shift($posRow);
			if ($tmPos < $nextMinPos && strpos($lastStr, $tmPosRow['str']) !== false) {
				continue;
			}
			$result[$tmPos] = $tmPosRow;
			$nextMinPos = $tmPos + $tmPosRow['len'];
			$lastStr = $tmPosRow['str'];
		}
		return $result;
	}

	public static function array_key_value_shift($arr) {
		if (empty($arr)) {
			return [];
		}
		return [
			'len' => array_keys($arr)[0],
			'str' => array_values($arr)[0]
		];
	}
}
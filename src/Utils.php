<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 17. 11. 2014
 * Time: 2:06
 */

namespace Trejjam\Utils;

use Nette,
	Trejjam;

class Utils
{
	/**
	 * @param string      $freeText
	 * @param double      $price
	 * @param null|string $units
	 * @param int         $decimalLength
	 * @return string
	 */
	public static function priceFreeText($freeText, $price, $units = NULL, $decimalLength = 2)
	{
		return $price <= 0 ? $freeText : self::priceCreate($price, $units, $decimalLength);
	}
	/**
	 * @param double      $price
	 * @param null|string $units
	 * @param int         $decimalLength
	 * @return string
	 */
	public static function priceCreate($price, $units = NULL, $decimalLength = 2)
	{
		$workPrice = floor(abs($price * pow(10, $decimalLength)));
		$integerPrice = floor($workPrice / pow(10, $decimalLength));
		$integerLength = strlen($integerPrice);
		$decimalPrice = self::numberAt($workPrice, 0, $decimalLength);

		$integerTernary = ceil($integerLength / 3);
		$decimalTernary = ceil($decimalLength / 3);

		$outPrice = '';
		for ($i = $integerTernary - 1; $i >= 0; $i--) {
			if ($outPrice != "") $outPrice .= '.';
			$outPrice .= self::numberAt($integerPrice, $i * 3, 3);
		}

		$outDecimalPrice = '';
		for ($i = $decimalTernary - 1; $i >= 0; $i--) {
			if ($outDecimalPrice != "") $outPrice .= '.';
			$decimalPosition = ($decimalLength - ($i + 1) * 3);
			$decimalPosition = $decimalPosition < 0 ? 0 : $decimalPosition;
			$outDecimalPrice .= self::numberAt($decimalPrice, $decimalPosition, 3);
		}

		return ($price < 0 ? '-' : '') . $outPrice . ',' . (in_array($outDecimalPrice, ['', '0']) ? '-' : $outDecimalPrice) . (is_null($units) ? '' : ' ' . $units);
	}
	/**
	 * @param int $number
	 * @param int $positionStart
	 * @param int $numberLength
	 * @return int
	 */
	public static function numberAt($number, $positionStart, $numberLength = 1)
	{
		return (int)(floor($number / pow(10, $positionStart))) % pow(10, $numberLength);
	}
	/**
	 * @param $zip
	 * @return string
	 */
	public static function unifyZip($zip)
	{
		if (strlen($zip) == 5) {
			return substr($zip, 0, 3) . " " . substr($zip, 3, 2);
		}

		return $zip;
	}
	/**
	 * @param string $phone
	 * @param bool   $addPrefix
	 * @param string $prefix
	 * @return string
	 */
	public static function unifyPhone($phone, $addPrefix = TRUE, $prefix = "+420")
	{
		$trimPhone = str_replace(' ', '', $phone);

		while (strlen($trimPhone) > 9 && $trimPhone[0] == '0') {
			$trimPhone = substr($trimPhone, 1);
		}

		$out = '';
		for ($i = strlen($trimPhone); $i >= 3; $i -= 3) {
			if ($out != '') $out = ' ' . $out;
			$out = substr($trimPhone, $i - 3, 3) . $out;

			if ($i < 6) {
				$out = substr($trimPhone, 0, $i - 3) . $out;
			}
		}

		if ($addPrefix && strlen($out) < 12 && $out != '') {
			$out = $prefix . ' ' . $out;
		}

		return $out;
	}

	/**
	 * @return array
	 */
	public static function getServerInfo()
	{
		$info = [
			"HTTP_ORIGIN"           => isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "",
			"HTTP_USER_AGENT"       => isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "",
			"REDIRECT_QUERY_STRING" => isset($_SERVER["REDIRECT_QUERY_STRING"]) ? $_SERVER["REDIRECT_QUERY_STRING"] : "",
			"QUERY_STRING"          => isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "",
		];

		return $info;
	}
	public static function getTextServerInfo()
	{
		return print_r(self::getServerInfo(), TRUE);
	}


	public static function getValue(array $array, $key, $keyDelimiter = '.')
	{
		$out = $array;
		$keyArray = explode($keyDelimiter, $key);

		foreach ($keyArray as $v) {
			if (isset($out[$v])) {
				$out = $out[$v];
			}
			else {
				throw new Trejjam\Utils\LogicException("Key '$v' from '$key' not exist in array.", Exception::UTILS_KEY_NOT_FOUND);
			}
		}

		return $out;
	}

	public static function getModuleFromRequest(Nette\Application\Request $request, $outputModuleDelimiter = ':')
	{
		$presenterArr = explode(':', $request->getPresenterName());
		array_pop($presenterArr);
		foreach ($presenterArr as $k => $v) {
			$presenterArr[$k] = Nette\Utils\Strings::firstLower($v);
		}

		$module = implode($outputModuleDelimiter, $presenterArr);

		return $module;
	}

	public static function getPresenterFromRequest(Nette\Application\Request $request)
	{
		$presenterArr = explode(':', $request->getPresenterName());

		return array_pop($presenterArr);
	}
}

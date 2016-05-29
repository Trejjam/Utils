<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 29.6.15
 * Time: 17:14
 */

namespace Trejjam\Utils\Helpers;

use Nette,
	Trejjam;

interface IBaseList
{
	function getList(array $sort = NULL, array $filter = NULL, $limit = NULL, $offset = NULL, array $defaultFilterType = [], array $filterTranslate = []);
	function getItem($id);
	function getCount(array $filter = NULL, array $defaultFilterType = [], array $filterTranslate = []);
}

<?php

use App\Models\ApprovalConfigModel;
use App\Models\AttendenceExceptModel;
use App\Repositories\ActivityLogRepository;

/**
 * 返回JSON
 * $code: 返回code
 * $data: 返回数据
 * $other: 额外信息
 */
function codeRender($code, $data = '', $message = '', $other = '')
{
	$status = 200;
	$message = empty($message) ? trans('error')[$code] : $message;
	
	$content = [
		'code' => $code,
		'message' => $other . $message,
		'data' => $data
	];
	return response($content, $status);
}

function page($model, $currentPage = 1)
{
	if (!$currentPage) {
		$currentPage = 1;
	}
	return [
		'total' => (int)$model->total(),
		'total_page' => (int)$model->lastPage(),
		'current_page' => (int)$currentPage,
		'per_page_num' => (int)$model->count(),
		'data' => $model->toArray()['data']
	];
}


function get_per_page($per_page)
{
	if ($per_page && $per_page <= 1000) {
		return $per_page;
	}
	return 1000;
}

/**
 * 计算分页的公共方法
 * @param number page ： 当前第几页，默认1
 * @param number per_page ： 每页多少条，默认10
 * @return ： 查询分页的字符串
 */
function commonPage($arr) 
{
	$pageNo = $arr['page'] ?? 1;
	$pageSize = $arr['per_page'] ?? env('FRONT_PAGE_SIZE');
	$offset = intval($pageSize) * (intval($pageNo) - 1);
	
	return [
		'page_size' => intval($pageSize),
		'offset' => $offset,
		'current_page' => intval($pageNo)
	];
}

/**
 * 计算分页的公共方法
 * @param number page ： 当前第几页，默认1
 * @param number per_page ： 每页多少条，默认10
 * @return ： 查询分页的字符串
 */
function commonPageV2($arr)
{
    $pageNo = empty($arr['page'] ) ?  1 : $arr['page'];
    $pageSize = empty($arr['per_page'] ) ?  env('FRONT_PAGE_SIZE') : $arr['per_page'];;
    $offset = intval($pageSize) * (intval($pageNo) - 1);

    return [
        'per_page' => intval($pageSize),
        'offset' => $offset,
        'current_page' => intval($pageNo)
    ];
}

/**
 * 返回分页数据封装
 */
function returnPage($data = [], $count = 0)
{
	$data = $data ?? [];
	$count = $count ?? 0;
	
	return [
		'list' => $data,
		'count' => $count
	];
}

/**
 * 返回到官网使用
 */
function returnPage2($data = [], $count = 0, $page = [])
{
	$data = $data ?? [];
	$count = $count ?? 0;
	$pageSize = $page['page_size'];
	$pageCount = ceil($count/$pageSize);
	
	return [
		'total' => $count, // 总数
		'total_page' => $pageCount, // 总页数
		'current_page' => $page['current_page'], // 当前页
		'per_page_num' => $pageSize, // 每页条数
		'data' => $data
	];
}

/**
 * 返回到官网使用
 */
function returnPageV3($data = [], $count = 0, $page = [])
{
    $data = $data ?? [];
    $count = $count ?? 0;
    $pageSize = $page['per_page'];
    $pageCount = ceil($count/$pageSize);

    return [
        'total' => $count, // 总数
        'total_page' => $pageCount, // 总页数
        'current_page' => $page['current_page'], // 当前页
        'per_page_num' => $pageSize, // 每页条数
        'data' => $data
    ];
}


/**
 * 加密
 */
function encryption($str) {
	return md5($str);
}

/**
 * 6位验证码
 */
function smscode() {
	return mt_rand(100000, 999999);
}

/**
 * 业务ID
 * business id
 * 规则：服务名称+时间戳+随机数 用"-"分开
 */
function businessId() {
	return BASE_NAME . '-' . time() . '-' . rand(100000, 999999);
}

/**
 * sign 随机字符串
 */
function signRandom($len = 10) {
	return str_random($len);
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author
 */
function getTree($list, $pk='id', $pid = 'parent_id', $child = 'children', $root = 0) {
	// 创建Tree
	$tree = [];
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = [];
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}

		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 根据某分类id查找所有的父类
 * @param $id
 * @param null $data
 * @param array $parents
 * @param int $include_self
 * @return array
 */
function findParents($id, $data = null, $parents = [])
{
	if (is_array($data)) {
		foreach ($data as $key => $one) {
			if (!empty($one['children'])) {//朝下继续找
				foreach ($one['children'] as $key2 => $value2) {
					if ($value2['id'] == $id) {
						$one['children'] = $value2;
						return $one;
					}
				}
			}
// 			if ($one['id'] == $id) {
// 				unset($one['children']);
// 				$parents[] = $one;
// 				return [true, $parents];
// 			} else {
// 				if (!empty($one['children'])) {//朝下继续找
// 					$child = $one['children'];
// 					unset($one['children']);
// 					$parents[] = $one;
// 					$result = findParents($id, $child, $parents);
// 					if ($result[0] == true) {
// 						return $result;
// 					} else {
// 						array_pop($parents);
// 					}
// 				}
// 			}
		}
	}
// 	return [false, $parents];
	return [];
}

/**
 * 存入登陆用户信息-运营端
 * @param unknown $arr
 */
function setLoginStaff($arr) {
	app()->instance(LOGIN_STAFF_KEY, $arr);
}

/**
 * 获取登陆用户信息
 * @param unknown $key
 */
function getLoginStaff($key = null) {
	$result = [];
	try {
		$result = app(LOGIN_STAFF_KEY);
	} catch (Exception $e) {
		return [];
	}
	
	if (!is_null($key)) {
		return empty($result[$key]) ? '' : $result[$key];
	}
	return $result;
}

/**
 * 存入登陆用户信息-官网
 * @param unknown $arr
 */
function setLoginHome($arr) {
	app()->instance(LOGIN_HOME_KEY, $arr);
}

/**
 * 获取登陆用户信息-官网
 * @param unknown $arr
 */
function getLoginHome($key = null) {
	$result = [];
	try {
		$result = app(LOGIN_HOME_KEY);
	} catch (Exception $e) {
		return [];
	}

	if (!is_null($key)) {
		return empty($result[$key]) ? '' : $result[$key];
	}
	return $result;
}

if (!function_exists('returnCreatedUpdatedAt')) {
    /**
     * FUNCTION_NAME : returnCreatedUpdatedAt
     * author : jp
     * 返回创建 和 更新时间
     * @return array
     */
    function returnCreatedUpdatedAt($key = '')
    {
        $time = time();

        if (!empty($key)) {
            return [$key => $time];
        }
        return [
            'created_at' => $time,
            'updated_at' => $time,
        ];
    }
}

if (!function_exists('buildEmptyPage')) {
    function buildEmptyPage($currentPage = 1)
    {
        if (!$currentPage) {
            $currentPage = 1;
        }
        return [
            'total' => 0,
            'total_page' => 1,
            'current_page' => $currentPage,
            'per_page_num' => 0,
            'data' => []
        ];
    }

}

/**
 * 审批配置
 * type 配置类型1园区管委会2非审计类主审部门3审计类主审部门
 */
function getApprovalConfig($type = null, $field = null) {
	static $list;
	/* 读取缓存数据 */
	if(empty($list)){
		$list = cache(CACHE_APPROVAL_CONFIG);
	}
	if(empty($list)){
		$data = ApprovalConfigModel::get(['config_value', 'config_type'])->toArray();;
		foreach ($data as $value) {
			$list[$value['config_type']] = $value;
		}
		cache([CACHE_APPROVAL_CONFIG => $list], 3600);
	}
	if(is_null($type)){
		return $list;
	} elseif(is_null($field)){
		return $list[$type];
	} else {
		return $list[$type][$field];
	}
}

/**
 * 判断日期为周1-5还是周6-7
 * @return true 周末  false 周1-5
 */
function getWeek($str) {
	$week = date('w', $str);
	$result = false;
	if ($week == 0 || $week == 6) {
		// 周末
		$result = true;
	}
	
	return $result;
}


/**
 * 判断日期是 是否为工作日
 * @return  true 工作日  false 非工作日
 */
function getAttendenceExcept($day) {
	static $list;
	/* 读取缓存数据 */
	if(empty($list)){
		$list = cache(CACHE_ATTENDENCE_EXCEPT);
	}
	if(empty($list)){
		$list = AttendenceExceptModel::where(['year' => date('Y')])
			->get(['id', 'start_time', 'end_time', 'type'])->toArray();;

		cache([CACHE_ATTENDENCE_EXCEPT => $list], 3600);
	}
	
	$isWorkDay = true;

	$week = getWeek($day);
	if ($week) {
		$isWorkDay = false;
		// 周末：判断：在列表中为工作日
		foreach ($list as $key => $value) {
			// type 类型1工作日放假2周末上班
			if ($value['type'] == 2
				&& $day >= $value['start_time']
				&& $day <= $value['end_time']) {
					
				$isWorkDay = true;
				break;
			}
		}
	} else {
		$isWorkDay = true;
		// 周一到周五：判断：不在列表中为工作日
		foreach ($list as $key => $value) {
			// type 类型1工作日放假2周末上班
			if ($value['type'] == 1
				&& $day >= $value['start_time']
				&& $day <= $value['end_time']) {
					
				$isWorkDay = false;
				break;
			}
		}
	}
	
	return $isWorkDay;
}

/**
 * 获取审批的开始时间和结束时间-主审部门
 * @param  $today
 */
function getStartEndTimeOne($today, $configStartDay, $configEndDay) {

	$currentDay= strtotime($today);
	// 计数
	$calX = (int)$configStartDay;

	while($calX > 0) {
		// 累加天数
		if (getAttendenceExcept($currentDay)) {
			$calX--;
		}
		$currentDay += 86400;
	}

	// 开始时间
	$startTime = $currentDay;

	// 计算结束时间
	$calX = (int)$configEndDay;

	while($calX > 0) {
		// 累加天数
		if (getAttendenceExcept($currentDay)) {
			$calX--;
		}
		// 包含当天
		if ($calX > 0) {
			$currentDay += 86400;
		}
	}

	// 开始时间
	$endTime = $currentDay;

	return [
		'start_time' => $startTime,
		'end_time' => $endTime
	];
}

/**
 * 计算花费时间-自己算工作日
 * @param  $today
 */
function getTakeUpTime($startTime, $endTime) 
{
	$time = $endTime - $startTime;
	if ($time <= 0) {
		return '0个工作日0小时0分0秒';
	}

	$result = '';
	//计算天数
	$days = intval($time/86400);
	if ($days > 0) {
		$workDays = 0;
		
		$currentDay= strtotime(date('Y-m-d', $startTime));
		while($days > 0) {
			// 累加天数
			$currentDay += 86400;
			if (getAttendenceExcept($currentDay)) {
				$workDays++;
			}
			$days--;
		}
		
		if ($workDays > 0) {
			$result .= $workDays . '个工作日';
		}
		
	}
	//计算小时数
	$remain = $time%86400;
	$hours = intval($remain/3600);
	if ($hours > 0) {
		$result .= $hours . '小时';
	}
	//计算分钟数
	$remain = $remain%3600;
	$mins = intval($remain/60);
	if ($mins > 0) {
		$result .= $mins . '分';
	}
	//计算秒数
	$secs = $remain%60;
	if ($secs > 0) {
		$result .= $secs . '秒';
	}
	return $result;
}

if (!function_exists('randomInteger')) {
        function randomInteger($length=1)
        {
            $rand = '';
            for ($i = 0; $i<$length;$i++) {
                $rand .= mt_rand(0,9);
            }
            return $rand;
        }
}

if (!function_exists('arr2obj')) {

    function arr2obj($data, $column)
    {
        if (is_string($column)) {
            if (isset($data[$column])) {
                $data[$column] = (object)$data[$column];
            }
        } elseif (is_array($column)) {
            foreach ($column as $key => $value) {
                if (isset($data[$value])) {
                    $data[$value] = (object)$data[$value];
                }
            }
        }

        return $data;
    }
}

if (!function_exists('downFile')) {
    function downFile($url, $path)
    {
        $fp_output = fopen($path, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp_output);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp_output);
    }
}

if (!function_exists('ip')) {
    /**
     * 获取客户端IP地址
     * @param integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function ip($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

/**
 * 处理日志
 */
function storeActivityLog($type, $subjectType, $subjectId, $attributes = [], $old = [])
{
	$titleArr = [
		ACTIVITY_TYPE['created'] => '新增',
		ACTIVITY_TYPE['updated'] => '编辑',
		ACTIVITY_TYPE['deleted'] => '删除',
	];
	
	$description = 'mysqlColumn';
	foreach (ACTIVITY_SUBJECT_TYPE as $key => $value) {
		if ($value == $subjectType) {
			$description .= '.' . $key;
			break;
		}
	}
	foreach (ACTIVITY_TYPE as $key => $value) {
		if ($value == $type) {
			$description .= '.' . $key;
			break;
		}
	}

   $causer_name = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
	app(ActivityLogRepository::class)->store([
		'type' => $type,
		'title' => $titleArr[$type],
		'description' => empty(trans($description)) ? '' : trans($description),
		'properties' => json_encode(['attributes' => $attributes, 'old' => $old]),
		'subject_type_id' => $subjectType,
		'subject_id' => $subjectId,
		'causer_id' => (int)getLoginStaff('id'),
		'causer_name' => $causer_name
	]);
}

/**
 * 截取字符串长度
 * @param string $str
 * @param number $len
 * @return string
 */
function getStrLength($str = '', $len = 20){
	$return = $str;
	if (iconv_strlen($str, 'UTF-8') >= $len) {
		$return = mb_substr($str, 0, $len - 3, 'UTF-8') . '...';
	}
	return $return;
}


/**
 * @method 多维数组转字符串
 */
function arrayToString($arr) {
	if (is_array($arr)){
		return implode(',', array_map('arrayToString', $arr));
	}
	return $arr;
}

// 字符处理-至获取英文和中文
function getEnglishAndChinese($str) {
	if (empty($str)) return '';
	preg_match_all("/[\x{4e00}-\x{9fa5}A-Za-z]+/u", $str, $matches);

	if (empty($matches[0])) return '';
	
	return implode('', $matches[0]);
}

/**
 * 字符切割
 */
function mbStrSplit($str, $len = 5000, $encodeing = 'UTF-8') {
	$result = [];
	if (empty($str)) return $result;
	
	$start = 0;
	$strlen = iconv_strlen($str, $encodeing);
	while ($strlen > 0) {
		$result[] = mb_substr($str, $start, $len, $encodeing);
		
		$strlen -= $len;
		$start += $len;
	}
	return $result;
}


if (!function_exists('isBase64')) {
    /**
     * FUNCTION_NAME : isBase64
     * author : jp
     * 判断字符串是发是base64
     * @param $str
     * @return bool
     */
    function isBase64($str)
    {
        return $str == base64_encode(base64_decode($str)) ? true : false;
    }
}


/**
 * 获取登陆用户部门信息
 * @param unknown $key
 */
function getLoginDepartment($key = null) {
    $result = [];
    try {
        $result = app(LOGIN_STAFF_DEPARTMENT_KEY);
    } catch (Exception $e) {
        return [];
    }

    if (!is_null($key)) {
        return empty($result[$key]) ? '' : $result[$key];
    }
    return $result;
}

/**
 * 存入登陆用户部门信息
 * @param unknown $arr
 */
function setLoginDepartment($arr) {
    app()->instance(LOGIN_STAFF_DEPARTMENT_KEY, $arr);
}


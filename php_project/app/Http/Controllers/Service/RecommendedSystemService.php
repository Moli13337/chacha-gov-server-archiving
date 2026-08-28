<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/11
 * Time: 15:33
 *
 * 推荐系统
 * 这里这涉及核心的推荐 不关心数据库的操作
 */

namespace App\Http\Controllers\Service;


class RecommendedSystemService extends BaseService
{

    /**
     * FUNCTION_NAME : jaccard
     * author : jp
     *
     */
    /**
     * FUNCTION_NAME : jaccard
     * author : jp
     * jaccard 相似度 两者的交集 / 两者的并集
     * 如果 两者为空 相似度是1 但是 这里 为了判断 直接取0
     *
     * @param array $target
     * @param array $source
     * @return float|int
     */
    public function jaccard(array $target, array $source)
    {
//        $target = array_filter(array_unique($target));
//        $source = array_filter(array_unique($source));

        $intersect = array_intersect($target, $source);
        $union = array_unique(array_merge($target, $source));

        $countIntersect = count($intersect);
        $countUnion = count($union);
        if (!$countIntersect || !$countUnion) {
            return 0;
        }
        return $countIntersect/ $countUnion;
    }
}
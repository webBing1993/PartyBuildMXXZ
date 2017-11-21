<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/25
 * Time: 17:35
 */

namespace app\home\model;
use think\Model;

class WechatUser extends Model {
    const POLITICS_STATUS_MASSES = 1;
    const POLITICS_STATUS_LEAGUE = 2;
    const POLITICS_STATUS_PARTY = 3;
    const POLITICS_ARRAY = [
        self::POLITICS_STATUS_MASSES  => '群众',
        self::POLITICS_STATUS_LEAGUE  => '团员',
        self::POLITICS_STATUS_PARTY  => '党员',
    ];
    public static function getName($userId) {
        return self::where('userId', $userId)->value('name');
    }
    public static function getTag($userId) {
        return self::where('userId', $userId)->value('tag');
    }
}
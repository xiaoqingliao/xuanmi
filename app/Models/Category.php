<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 栏目
 * @attribute id|integer
 * @attribute title|string          栏目名称
 * @attribute cover|string          栏目封面
 * @attribute type|integer          栏目类型
 * @attribute fields|array          栏目字段,备用
 * @attribute extensions|array      栏目扩展属性，备用
 * @attribute orderindex|integer    排序
 */
class Category extends Model
{
    use SoftDeletes;
    use ExtensionTrait;

    const TYPE_MEETING = 1; //会议
    const TYPE_COURSE = 2;  //课程
    const TYPE_ARTICLE = 3; //文章
    const TYPE_PRODUCT = 4; //产品
    const TYPE_COMPANY = 5; //公司
    const TYPE_PERSONAL = 6;  //个人详情
    const TYPE_ENT = 7; //企业简介
    const TYPE_BIZ = 8; //商务合作
    const TYPE_HONOR = 9;   //荣誉
    const TYPE_VIDEO = 10;  //宣传片 
    const TYPE_CASE = 11;   //案例
    const TYPE_PHOTO = 12;  //团队合影
    const TYPE_FAMOUS = 13;    //名人
    const TYPE_PLAN = 14;   //发展规划

    protected $fillable = ['title', 'cover', 'type', 'fields', 'extensions'];
    protected $casts = [
        'type' => 'integer',
        'fields' => 'array',
        'extensions' => 'array',
        'orderindex' => 'integer',
    ];

    public static function getTypes()
    {
        return [
            Category::TYPE_MEETING => '会议类型',
            Category::TYPE_COURSE => '课程类型',
            Category::TYPE_ARTICLE => '文章类型',
            Category::TYPE_PRODUCT => '产品类型',
            Category::TYPE_COMPANY => '企业展示类型',
        ];
    }

    public static function getCode($type)
    {
        $codes = [
            Category::TYPE_MEETING => 'meeting',
            Category::TYPE_COURSE => 'course',
            Category::TYPE_ARTICLE => 'article',
            Category::TYPE_PRODUCT => 'product',
            Category::TYPE_COMPANY => 'company',
        ];
        return isset($codes[$type]) ? $codes[$type] : 'meeting';
    }

    public static function getArticleTypes()
    {
        return [
            Category::TYPE_ARTICLE => '文章',
            Category::TYPE_PRODUCT => '产品展示',
            Category::TYPE_COMPANY => '企业展示',
            Category::TYPE_PERSONAL => '个人详情',
            Category::TYPE_ENT => '企业简介',
            Category::TYPE_BIZ => '商务合作',
            Category::TYPE_HONOR => '企业荣誉',
            Category::TYPE_VIDEO => '宣传片',
            Category::TYPE_CASE => '客户案例',
            Category::TYPE_PHOTO => '团队合影',
            Category::TYPE_FAMOUS => '名人见证',
            Category::TYPE_PLAN => '发展规划',
        ];
    }

    public static function getArticleTypeTitle($type) {
        $types = self::getArticleTypes();

        return isset($types[$type]) ? $types[$type] : '文章';
    }

    public static function getList($type)
    {
        return self::where('type', $type)->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
    }
}

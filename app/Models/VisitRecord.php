<?php
/**
* author: jumper swordwave
* copyright: 泽诚信息科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 浏览记录
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute model_type|string
 * @attribute model_id|integer
 * @attribute title|string
 * @attribute ip|string
 * @attribute areas|string
 * @attribute device|string
 * @attribute agent|string
 */
class VisitRecord extends Model
{
    const TYPE_MEMBER = 'member';
    const TYPE_MEETING = 'meeting';
    const TYPE_COURSE = 'course';
    const TYPE_ARTICLE = 'article';

    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'model_id' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class)->where('group_id', '>', 0);
    }

    public function updateViews()
    {
        $method = 'update' . ucfirst($this->model_type);
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    public function updateMeeting()
    {
        $meeting = Meeting::find($this->model_id);
        if ($meeting != null) {
            $meeting->clicks++;
            $meeting->save();
        }
    }
    
    public function updateCourse()
    {
        $course = Course::find($this->model_id);
        if ($course != null) {
            $course->clicks++;
            $course->save();
        }
    }
    
    public function updateArticle()
    {
        $article = Article::find($this->model_id);
        if ($article != null) {
            $article->clicks++;
            $article->save();
        }
    }

    public static function getTypeKeys()
    {
        return [self::TYPE_MEMBER, self::TYPE_MEETING, self::TYPE_COURSE, self::TYPE_ARTICLE];
    }
}

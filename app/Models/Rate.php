<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户评价
 * @attribute id|integer
 * @attribute member_id|integer
 * @attribute model_type|string
 * @attribute model_id|integer
 * @attribute content|string
 * @attribute score|integer
 */
class Rate extends Model
{
    protected $fillable = [];
    protected $casts = [
        'member_id' => 'integer',
        'model_id' => 'integer',
        'score' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function updateScore()
    {
        $method = 'update' . ucfirst($this->model_type);
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }
    
    private function updateArticle()
    {
        $object = Article::find($this->model_id);
        if ($object != null) {
            $object->scores += $this->score;
            $object->score_count += 1;
            $object->save();
        }
    }

    private function updateMeeting()
    {
        $object = Meeting::find($this->model_id);
        if ($object != null) {
            $object->scores += $this->score;
            $object->score_count += 1;
            $object->save();
        }
    }

    private function updateCourse()
    {
        $object = Course::find($this->model_id);
        if ($object != null) {
            $object->scores += $this->score;
            $object->score_count += 1;
            $object->save();
        }
    }
}

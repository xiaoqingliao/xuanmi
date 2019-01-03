<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/
namespace App\Http\Controllers\Api;

use App\Models\ApiErrorCode;
use App\Models\AppConstants;
use App\Models\Category;
use App\Models\Article;
use App\Models\FriendTimeline;

/**
 * 文章列表
 */
class ArticleController extends BaseController
{
    /**
     * 列表
     */
    public function index()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $member_id = intval(request('mchid'));
        $category_id = intval(request('categoryid'));
        $type = intval(request('type'));

        if ($member_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::MEMBER_ERROR]);
        }
        if ($type <= 0) $type = Category::TYPE_ARTICLE;
        
        $cursor = Article::where('member_id', $member_id)->where('status', AppConstants::ACCEPTED)->where('type', $type);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        $count = $cursor->count();
        $articles = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $articles->lastPage();

        $articles = $articles->map(function($article){
            $item = [
                'id' => $article->id,
                'title' => $article->title,
                'cover' => cover_url($article->cover ?: '!'.$article->video, null, null, true),
                'video' => media_url($article->video),
                'summary' => $article->getExtensions('summary', ''),
                'views' => $article->views,
                'score' => $article->score,
                'created' => $article->created_at->timestamp,
            ];
            if ($article->type == Category::TYPE_PRODUCT) {
                $item['origin'] = $article->getExtensions('origin', '');
                $item['price'] = $article->getExtensions('price', '');
            }
            return $item;
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$articles, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 分类
     */
    public function category()
    {
        $type = intval(request('type'));
        if ($type <= 0) $type = Category::TYPE_ARTICLE;
        $categories = Category::where('type', $type)->orderBy('orderindex', 'desc')->orderBy('id', 'asc')->get();
        $categories = $categories->map(function($category){
            return [
                'id' => $category->id,
                'title' => $category->title,
            ];
        })->toArray();
        return response()->json(['error'=>false, 'list'=>$categories]);
    }

    /**
     * 我的列表
     */
    public function my()
    {
        $page = intval(request('page', 1));
        $pagesize = intval(request('pagesize', 20));
        $category_id = intval(request('categoryid'));
        $type = intval(request('type'));
        $key = request('key');

        if ($type <= 0) $type = Category::TYPE_ARTICLE;
        $cursor = Article::where('type', $type)->where('member_id', $this->member->id);
        if ($category_id > 0) {
            $cursor->where('category_id', $category_id);
        }
        if ($key != '') {
            $cursor->where('title', 'like', '%'. $key .'%');
        }
        $count = $cursor->count();
        $articles = $cursor->orderBy('orderindex', 'desc')->orderBy('id', 'desc')->paginate($pagesize);
        $pages = $articles->lastPage();

        $articles = $articles->map(function($article){
            $item = [
                'id' => $article->id,
                'title' => $article->title,
                'cover' => image_url($article->cover, null, null, true),
                'video' => media_url($article->video),
                'views' => $article->views,
                'score' => $article->score,
                'created' => $article->created_at->timestamp,
            ];
            if ($article->type == Category::TYPE_PRODUCT) {
                $item['origin'] = $article->getExtensions('origin', '');
                $item['price'] = $article->getExtensions('price', '');
            }
            return $item;
        })->toArray();

        return response()->json(['error'=>false, 'list'=>$articles, 'count'=>$count, 'pages'=>$pages]);
    }

    /**
     * 添加
     */
    public function store()
    {
        $article_type_ids = array_keys(Category::getArticleTypes());
        $article = new Article();
        $article->member_id = $this->member->id;
        $article->type = intval(request('type'));
        if ($article->type <= 0) $article->type = Category::TYPE_ARTICLE;
        $article->category_id = intval(request('categoryid'));
        $article->title = request('title');
        $article->cover = image_replace(request('cover'));
        $banners = request('banners');
        if (is_array($banners)) $banners = implode(',', $banners);
        $article->banners = $banners;
        $content = request('content');
        if (is_array($content) == false) $content = json_decode($content, true);
        if (empty($content)) $content = [];
        $article->content = $content;
        $article->video = media_replace(request('video'));
        $article->extensions = request('extensions');
        $article->status = AppConstants::ACCEPTED;
        $article->base_clicks = intval(request('base_clicks'));
        $article->setExtensions('summary', request('summary', ''));
        $article->setExtensions('origin', request('origin', ''));   //产地
        $article->setExtensions('price', request('price', '')); //价格
        if (!in_array($article->type, $article_type_ids)) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_TYPE_ID_ERROR]);
        }
        if ($article->type == Category::TYPE_ARTICLE && $article->category_id <= 0) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_CATEGORY_ID_ERROR]);
        }
        if ($article->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_TITLE_ERROR]);
        }
        if ($article->cover == '') {
            $category = Category::find($article->category_id);
            if ($category != null) {
                $article->cover = $category->cover;
            }
        }
        /*if ($article->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_COVER_ERROR]);
        }*/
        if ($article->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_CONTENT_ERROR]);
        }
        $article->save();

        //发布动态
        $line = new FriendTimeline();
        $line->member_id = $article->member_id;
        $line->model_type = 'article';
        $line->model_id = $article->id;
        $line->title = $article->title;
        $line->cover = $article->cover;
        $line->content = '发布了' . Category::getArticleTypeTitle($article->type);
        $line->save();

        $this->member->searchWeightIncrement(config('site.article_weight', 1));

        return response()->json(['error'=>false]);
    }

    /**
     * 更新
     */
    public function update($id)
    {
        $article = Article::find($id);
        if ($article == null) {
            return respose()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }
        $article->category_id = intval(request('categoryid'));
        $article->title = request('title');
        $article->cover = image_replace(request('cover'));
        $banners = request('banners');
        if (is_array($banners)) $banners = implode(',', $banners);
        $article->banners = $banners;
        $content = request('content');
        if (is_array($content) == false) $content = json_decode($content, true);
        if (empty($content)) $content = [];
        $article->content = $content;
        $article->video = media_replace(request('video'));
        $article->extensions = request('extensions');
        $article->base_clicks = intval(request('base_clicks'));
        $article->setExtensions('summary', request('summary', ''));
        $article->setExtensions('origin', request('origin', ''));   //产地
        $article->setExtensions('price', request('price', '')); //价格
        if ($article->title == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_TITLE_ERROR]);
        }
        $category = Category::find($article->category_id);
        if ($category != null && $category->cover != '') {
            $article->cover = $category->cover;
        }
        /*if ($article->cover == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_COVER_ERROR]);
        }*/
        if ($article->content == '') {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::ARTICLE_CONTENT_ERROR]);
        }

        $article->save();
        return response()->json(['error'=>false]);
    }

    /**
     * 删除
     */
    public function destroy($id)
    {
        $article = Article::find($id);
        if ($article != null && $article->member_id = $this->member->id) {
            FriendTimeline::where('member_id', $article->member_id)->where('model_type', 'article')->where('model_id', $article->id)->delete();
            $article->delete();
        }
        return response()->json(['error'=>false]);
    }

    /**
     * 详情
     */
    public function show($id)
    {
        $article = Article::find($id);
        if ($article == null) {
            return response()->json(['error'=>true, 'code'=>ApiErrorCode::NOTFOUND]);
        }

        return response()->json(['error'=>false, 'article'=>$article->toArrayShow()]);
    }

    /**
     * 排序
     */
    public function updateOrder()
    {
        $items = request('items');
        if (!is_array($items)) $items = [];

        foreach($items as $item){
            if (isset($item['id']) && isset($item['index'])){
                Article::where('id', intval($item['id']))->where('member_id', $this->member->id)->update(['orderindex'=>intval($item['index'])]);
            }
        }
        return response()->json(['error'=>false]);
    }
}

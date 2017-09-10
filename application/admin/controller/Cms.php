<?php
namespace app\admin\controller;

use data\model\AlbumPictureModel as AlbumPictureModel;
use data\service\Article;

/**
 * cms内容管理系统
 */
class Cms extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 文章列表
     */
    public function articleList()
    {
        
        $child_menu_list = array(
            array(
                'url' => "cms/articlelist",
                'menu_name' => "文章列表",
                "active" => 1
            ),
            array(
                'url' => "cms/articleclasslist",
                'menu_name' => "文章分类",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        if (request()->isAjax()) {
            $article = new Article();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition = array(
                'title|name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $result = $article->getArticleList($page_index, $page_size, $condition, 'nca.sort asc');
            return $result;
        } else {
            return view($this->style . 'Cms/articleList');
        }
    }

    /**
     * 添加文章
     */
    public function addArticle()
    {
        if (request()->isAjax()) {
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $short_title = isset($_POST['short_title']) ? $_POST['short_title'] : '';
            $source = isset($_POST['source']) ? $_POST['source'] : '';
            $url = isset($_POST['url']) ? $_POST['url'] : '';
            $author = isset($_POST['author']) ? $_POST['author'] : '';
            $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $image = isset($_POST['image']) ? $_POST['image'] : '';
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
            $article_id_array = isset($_POST['article_id_array']) ? $_POST['article_id_array'] : '';
            $click = isset($_POST['click']) ? $_POST['click'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $commend_flag = isset($_POST['commend_flag']) ? $_POST['commend_flag'] : '';
            $comment_flag = isset($_POST['comment_flag']) ? $_POST['comment_flag'] : '';
            $attachment_path = isset($_POST['attachment_path']) ? $_POST['attachment_path'] : '';
            $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
            $comment_count = isset($_POST['comment_count']) ? $_POST['comment_count'] : '';
            $share_count = isset($_POST['share_count']) ? $_POST['share_count'] : '';
            $share_count = isset($_POST['share_count']) ? $_POST['share_count'] : '';
            $status = 2;
            $article = new Article();
            $result = $article->addArticle($title, $class_id, $short_title, $source, $url, $author, $summary, $content, $image, $keyword, $article_id_array, $click, $sort, $commend_flag, $comment_flag, $status, $attachment_path, $tag, $comment_count, $share_count);
            return AjaxReturn($result);
        } else {
            $article = new Article();
            $articleClassList = $article->getArticleClass();
            $this->assign('articleClassList', $articleClassList);
            return view($this->style . 'Cms/addArticle');
        }
    }

    /**
     * 修改文章
     */
    public function updateArticle()
    {
        $article_id = isset($_GET['id']) ? $_GET['id'] : '';
        $article = new Article();
        $ArticleDetail = $article->getArticleDetail($article_id);
        $attachment_path = explode(",", $ArticleDetail['attachment_path']);
        $articleClassList = $article->getArticleClass();
        $this->assign('attachment_path', $attachment_path[0]);
        $this->assign('article_id', $article_id);
        $this->assign('articleClassList', $articleClassList);
        $this->assign('ArticleDetail', $ArticleDetail);
        // 查询图片表
       // $goods_img = new AlbumPictureModel();
      //  $imginfo = $goods_img->all($ArticleDetail['image']);
      //  $img = empty($imginfo) ? '' : $imginfo[0]["pic_cover_big"];
      //  $this->assign('img', $img);
        return view($this->style . 'Cms/updateArticle');
    }

    /**
     * 处理文章
     */
    public function ajax_updateArticle()
    {
        $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
        $short_title = isset($_POST['short_title']) ? $_POST['short_title'] : '';
        $source = isset($_POST['source']) ? $_POST['source'] : '';
        $url = isset($_POST['url']) ? $_POST['url'] : '';
        $author = isset($_POST['author']) ? $_POST['author'] : '';
        $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $image = isset($_POST['image']) ? $_POST['image'] : '';
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $article_id_array = isset($_POST['article_id_array']) ? $_POST['article_id_array'] : '';
        $click = isset($_POST['click']) ? $_POST['click'] : '';
        $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
        $commend_flag = isset($_POST['commend_flag']) ? $_POST['commend_flag'] : '';
        $comment_flag = isset($_POST['comment_flag']) ? $_POST['comment_flag'] : '';
        $attachment_path = isset($_POST['attachment_path']) ? $_POST['attachment_path'] : '';
        $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
        $comment_count = isset($_POST['comment_count']) ? $_POST['comment_count'] : '';
        $share_count = isset($_POST['share_count']) ? $_POST['share_count'] : '';
        $status = 2;
        $article = new Article();
        $result = $article->updateArticle($article_id, $title, $class_id, $short_title, $source, $url, $author, $summary, $content, $image, $keyword, $article_id_array, $click, $sort, $commend_flag, $comment_flag, $status, $attachment_path, $tag, $comment_count, $share_count);
        return AjaxReturn($result);
    }

    /**
     * 文章分类列表
     */
    public function articleClassList()
    {
        $child_menu_list = array(
            array(
                'url' => "cms/articlelist",
                'menu_name' => "文章列表",
                "active" => 0
            ),
            array(
                'url' => "cms/articleclasslist",
                'menu_name' => "文章分类",
                "active" => 1
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $article = new Article();
        $article_list = $article->getArticleClassQuery();
        $this->assign('list', $article_list);
        return view($this->style . 'Cms/articleClassList');
    }

    /**
     * 根据文章id获取文章详情
     */
    public function articleClassInfo()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $infolist = $article->getArticleClassDetail($class_id);
            return $infolist;
        }
    }

    /**
     * 文章分类添加修改
     *
     * @return multitype:unknown @class_id 大于0就是修改，小于等于0就是添加
     */
    public function addUpdateAritcleClass()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $pid = isset($_POST['pid']) ? $_POST['pid'] : '';
            if ($class_id > 0) {
                $retval = $article->updateArticleClass($class_id, $name, $sort, $pid);
            } else {
                $retval = $article->addAritcleClass($name, $sort, $pid);
            }
            return AjaxReturn($retval);
        }
    }

    /**
     * 文章分类删除
     */
    public function deleteArticleClass()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            /*
             * if ($article->articleClassUseCount($class_id) > 0) {
             * $retval = array(
             * 'code' => '0',
             * 'message' => '分类已被使用不可删除'
             * );
             * return $retval;
             * } else {
             * $retval = $article->deleteArticleClass($class_id);
             * return AjaxReturn($retval);
             * }
             */
            $retval = $article->deleteArticleClass($class_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 文章删除
     */
    public function deleteArticle()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
            $retval = $article->deleteArticle($article_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 评论列表
     */
    public function commentArticle()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $commentArticleList = $article->getCommentList($page_index, $page_size, '', 'up desc');
            return $commentArticleList;
        } else {
            return view($this->style . 'Cms/commentArticle');
        }
    }

    /**
     * 评论详情
     */
    public function comment_detaile()
    {
        if (request()->isAjax()) {
            $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '';
            $article = new Article();
            $result = $article->getCommentDetail($comment_id);
            return $result;
        }
    }

    /**
     * 删除单条评论
     */
    public function comment_delete()
    {
        if (request()->isAjax()) {
            $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '';
            $article = new Article();
            $result = $article->deleteComment($comment_id);
            return AjaxReturn($result);
        }
    }

    /**
     * 修改分类排序
     */
    public function modifyField()
    {
        if (request()->isAjax()) {
            $class_id = isset($_POST['fieldid']) ? $_POST['fieldid'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $article = new Article();
            $result = $article->modifyArticleClassSort($class_id, $sort);
            return AjaxReturn($result);
        }
    }

    /**
     * 修改文章列表排序
     */
    public function modifyArticleField()
    {
        if (request()->isAjax()) {
            $class_id = isset($_POST['fieldid']) ? $_POST['fieldid'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $article = new Article();
            $result = $article->modifyArticleSort($class_id, $sort);
            return AjaxReturn($result);
        }
    }

    /**
     * 专题列表
     * 任鹏强
     * 2017年6月5日10:48:18
     */
    public function topicList()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $retval = $article->getTopicList($page_index, $page_size, '', '');
            return $retval;
        } else {
            return view($this->style . 'Cms/topicList');
        }
    }

    /**
     * 添加专题
     * 任鹏强
     * 2017年6月5日10:48:33
     */
    public function addTopic()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $instance_id = $this->instance_id;
            $title = request()->post('title', '');
            $image = request()->post('image', '');
            $content = request()->post('content', '');
            $status = request()->post('status', '');
            $topic = $article->addTopic($instance_id, $title, $image, $content, $status);
            return AjaxReturn($topic);
        } else {
            return view($this->style . 'Cms/addTopic');
        }
    }

    /**
     * 修改专题
     * 任鹏强
     * 2017年6月5日10:49:15
     * 
     * @return Ambigous <\data\model\cms\unknown, \data\model\multitype:unknown, multitype:unknown number >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateTopic()
    {
        if (request()->isAjax()) {
            $article = new Article();
            $instance_id = $this->instance_id;
            $topic_id = request()->post('topic_id', '');
            $title = request()->post('title', '');
            $image = request()->post('image', '');
            $content = request()->post('content', '');
            $status = request()->post('status', '');
            $res = $article->updateTopic($instance_id, $topic_id, $title, $image, $content, $status);
            return AjaxReturn($res);
        } else {
            $article = new Article();
            $topic_id = request()->get('id', '');
            $info = $article->getTopicDetail($topic_id);
            $this->assign('info', $info);
            return view($this->style . 'Cms/updateTopic');
        }
    }

    /**
     * 删除专题
     */
    public function deleteTopic()
    {
        if (request()->isAjax()) {
            $article = new Article();
            // $instance_id=$this->instance_id;
            $topic_id = request()->post('topic_id', '');
            $res = $article->deleteTopic($topic_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 修改单个字段
     */
    public function cmsField()
    {
        $class_id = $_POST['fieldid'] ? $_POST['fieldid'] : 0;
        $sort = $_POST['fieldname'];
        $name = $_POST['fieldvalue'];
        $article = new Article();
        $retval = $article->cmsField($class_id, $sort, $name);
        return AjaxReturn($retval);
    }
}
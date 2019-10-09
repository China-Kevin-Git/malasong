<?phpnamespace app\admin\controller\match;use app\admin\controller\AuthController;use service\FormBuilder as Form;use service\UtilService as Util;use service\JsonService as Json;use service\UploadService as Upload;use think\Db;use think\Request;use think\Url;use app\admin\model\article\ArticleCategory as ArticleCategoryModel;use app\admin\model\article\Article as ArticleModel;/** * 文章分类管理  控制器 * */class MatchCategory extends AuthController{    /**     * 分类管理     * */     public function index(){         $match_catrgory =  Db::name("match_catrgory")->select();         $this->assign("list",$match_catrgory);         return $this->fetch();     }    /**     * 添加分类管理     * */    public function create(){        $f = array();        $f[] = Form::input('name','分类名称');        $form = Form::make_post_form('添加分类',$f,Url::build('save'));        $this->assign(compact('form'));        return $this->fetch('public/form-builder');    }    /**     * s上传图片     * */    public function upload(){        $res = Upload::image('file','article');        $thumbPath = Upload::thumb($res->dir);        if($res->status == 200)            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);        else            return Json::fail($res->error);    }    /**     * 保存分类管理     * */    public function save(Request $request){        $data = Util::postMore([            'name',          ],$request);        if(!$data['name']) return Json::fail('请输入分类名称');        $new_id = Db::name("match_catrgory")->insert(["name"=>$data['name']]);        if(!$new_id) return Json::fail('文章列表添加失败');        return Json::successful('添加分类成功!');    }    /**     * 修改分类     * */    public function edit($id){        if(!$id) return $this->failed('参数错误');        $article= Db::name("match_catrgory")->where(["id"=>$id])->find();        if(!$article) return Json::fail('数据不存在!');        $f = array();        $f[] = Form::input('name','分类名称',$article['name']);        $form = Form::make_post_form('编辑分类',$f,Url::build('update',array('id'=>$id)));        $this->assign(compact('form'));        return $this->fetch('public/form-builder');    }    public function update(Request $request, $id)    {        $data = Util::postMore([            'name',        ],$request);        if(!$data['name']) return Json::fail('请输入分类名称');         Db::name("match_catrgory")->where(["id"=>$id])->update(["name"=>$data["name"]]);        return Json::successful('修改成功!');    }    /**     * 删除分类     * */    public function delete($id)    {        $res = Db::name("match_catrgory")->where(["id"=>$id])->delete();        if(!$res)            return Json::fail(ArticleCategoryModel::getErrorInfo('删除失败,请稍候再试!'));        else            return Json::successful('删除成功!');    }}
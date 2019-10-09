{extend name="public/container"}
{block name="content"}
<div class="row">

    <div class="col-sm-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <button type="button" class="btn btn-w-m btn-primary add-filed">添加套餐</button>

            </div>
            <div class="ibox-content">

                <div class="row">
                    <div class="m-b m-l">
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table table-striped  table-bordered">

                        <thead>

                        <tr>

                            <th>编号</th>
                            <th>赛事套餐标题</th>
                            <th>赛事名称</th>
                            <th>套餐价格</th>
                            <th>图片</th>

                            <th>操作</th>

                        </tr>

                        </thead>

                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.meal_id}
                            </td>

                            <td class="text-center">
                                {$vo.title}

                            </td>

                            <td class="text-center">
                                {$vo.match_name}

                            </td>

                            <td class="text-center">
                                {$vo.price}

                            </td>

                            <td class="text-center" height="6%">
                                <img src="{$vo.logo}" width="10%">

                            </td>

                            <td class="text-center">

                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['meal_id']))}')"><i class="fa fa-paste"></i> 编辑</button>

                                <button class="btn btn-warning btn-xs del_config_tab" data-id="{$vo.meal_id}" type="button" data-url="{:Url('delete',array('id'=>$vo['meal_id']))}" ><i class="fa fa-warning"></i> 删除</button>
                                <a type="button" class="btn btn-info btn-xs"  onclick="$eb.createModalFrame(this.innerText,'{:Url('edit_content')}?id={$vo['meal_id']}')"> <i class="fa fa-paste"></i> 详情</a>


                            </td>

                        </tr>

                        {/volist}

                        </tbody>

                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
{/block}
{block name="script"}
<script>

    $('.image_info').on('click',function (e) {
        var image_url = $(this).data('image');
        $eb.openImage(image_url);
    })
    $('.add-filed').on('click',function (e) {
        $eb.createModalFrame(this.innerText,"{:Url('create')}");
    })
    $('.del_config_tab').on('click',function(){

        var _this = $(this),url =_this.data('url');

        $eb.$swal('delete',function(){

            $eb.axios.get(url).then(function(res){

                if(res.status == 200 && res.data.code == 200) {

                    $eb.$swal('success',res.data.msg);

                    _this.parents('tr').remove();

                }else

                    return Promise.reject(res.data.msg || '删除失败')

            }).catch(function(err){

                $eb.$swal('error',err);

            });

        })

    });
    $('.add_filed_base').on('click',function (e) {
        $eb.swal({
            title: '请选择数据类型',
            input: 'radio',
            inputOptions: ['文本框','多行文本框','单选框','文件上传','多选框'],
            inputValidator: function(result) {
                return new Promise(function(resolve, reject) {
                    if (result) {
                        resolve();
                    } else {
                        reject('请选择数据类型');
                    }
                });
            }
        }).then(function(result) {
            if (result) {
                $eb.createModalFrame(this.innerText,"{:Url('SystemConfig/create')}?type="+result);
            }
        })
    })
</script>
{/block}
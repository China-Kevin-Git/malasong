{extend name="public/container"}
{block name="content"}
<div class="row">

    <div class="col-sm-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <button type="button" class="btn btn-w-m btn-primary add-filed">添加赛事</button>

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
                            <th>赛事昵称</th>
                            <th>赛事结束时间</th>
                            <th>报名截止时间</th>
                            <th>地址</th>
                            <th>开启活动</th>
                            <th>操作</th>

                        </tr>

                        </thead>

                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.id}
                            </td>

                            <td class="text-center">
                                {$vo.match_name}

                            </td>

                            <td class="text-center">
                                {$vo.match_stop}

                            </td>

                            <td class="text-center">
                                {$vo.enroll_time}

                            </td>

                            <td class="text-center">
                                {$vo.address}

                            </td>
                            <td class="text-center">
                                <a class="btn  btn-xs" href="javascript:void(0);" onclick="$eb.createModalFrame(this.innerText,'{:Url('umps.match_seckill/seckill')}?id={$vo['id']}')"">
                                <i class="fa fa-gavel"></i> 开启秒杀</a>
                                <a class="btn  btn-xs" href="javascript:void(0);" onclick="$eb.createModalFrame(this.innerText,'{:Url('umps.match_bargain/bargain')}?id={$vo['id']}')">
                                    <i class="fa fa-sort-amount-asc"></i> 开启砍价</a>
                                <a class="btn  btn-xs" href="javascript:void(0);" onclick="$eb.createModalFrame(this.innerText,'{:Url('umps.match_combination/combination')}?id={$vo['id']}')">
                                    <i class="fa fa-hand-lizard-o"></i> 开启拼团</a>
                            </td>
                            <td class="text-center">

                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')"><i class="fa fa-paste"></i> 编辑</button>
                                <button class="btn btn-warning btn-xs del_config_tab" data-id="{$vo.id}" type="button" data-url="{:Url('delete',array('id'=>$vo['id']))}" ><i class="fa fa-warning"></i> 删除</button>
                                <a type="button" class="btn btn-info btn-xs"  onclick="$eb.createModalFrame(this.innerText,'{:Url('edit_content')}?id={$vo['id']}')"> <i class="fa fa-paste"></i> 详情</a>

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
    });

    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute',
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute',

            }).toggle();
        }
    }
</script>
{/block}
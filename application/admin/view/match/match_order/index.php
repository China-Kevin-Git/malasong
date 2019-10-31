{extend name="public/container"}
{block name="content"}
<div class="row">

    <div class="col-sm-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">


            </div>
            <div class="ibox-content">

                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">

                            <div class="input-group">
                                <input type="text" name="keyword"  placeholder="请输入订单号" class="input-sm form-control"> <span class="input-group-btn">
                                      <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i>搜索</button> </span>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table table-striped  table-bordered">

                        <thead>

                        <tr>

                            <th>编号</th>
                            <th>订单号</th>
                            <th>赛事名字</th>
                            <th>用户</th>
                            <th>价格</th>
                            <th>支付状态</th>
                            <th>是否完善资料</th>
                            <th>选择的赛程</th>
                            <th>购买的套餐</th>
                            <th>购买的服务</th>
                            <th>操作</th>

                        </tr>

                        </thead>

                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>

                            <td class="text-center">
                                {$vo.match_order_id}
                            </td>

                            <td class="text-center">
                                {$vo.match_order_sn}

                            </td>
                            <td class="text-center">
                                {$vo.match_name}

                            </td>
                            <td class="text-center">
                                {$vo.nickname}

                            </td>
                            <td class="text-center">
                                {$vo.order_price}

                            </td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.is_pay' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>

                            <td class="text-center">
                                <i class="fa {eq name='vo.status' value='0'}fa-check text-navy{/eq}{eq name='vo.status' value='1'}fa-check text-navy{/eq}{eq name='vo.status' value='3'}fa-close text-danger{/eq}{eq name='vo.status' value='2'}fa-close text-danger{/eq}"></i>
                            </td>
                            <td class="text-center">
                                {$vo.red_name}

                            </td>
                            <td class="text-center">
                                {$vo.meal_name}

                            </td>
                            <td class="text-center">
                                {$vo.service_name}

                            </td>

                            <td class="text-center">
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('查看资料','{:Url('means',array('id'=>$vo['match_order_id']))}')"><i class="fa fa-paste"></i> 查看资料</button>
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('查看资料','{:Url('edit',array('id'=>$vo['match_order_id']))}')"><i class="fa fa-paste"></i> 中签信息</button>
                                <button class="btn btn-warning btn-xs del_config_tab" data-id="{$vo.match_order_id}" type="button" data-url="{:Url('delete',array('id'=>$vo['match_order_id']))}" ><i class="fa fa-warning"></i> 删除</button>
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
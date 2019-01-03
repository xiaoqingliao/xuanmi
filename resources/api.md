## 炫秘接口
> 地址：{server}/api/

> 公共参数：
>> token:   登录接口返回的access_token，注册有效期
>> formid:  小程序表单id

> 公共返回值：
>> error：  请求结果    true成功/false失败

> 错误码：见最后

### 图片上传
> upload

> token验证：   true

> method: post

> 参数：
>> file:    文件

> 返回值：
>>  url:     图片地址   
>>  title:   图片名称

### 系统默认设置参数
> setting

> 参数：无

> 返回值：
>>  setting
>>>     trail-days: 注册后可试用时间
>>>     reg_price:  首次注册费用
>>>     renew_price:普通会员续费费用
>>>     order_rate: 用户下单平台抽成百分比
>>>     withdraw_money: 提现最低金额
>>>     withdraw_money_fee: 提现手续费百分比
>>>     withdraw_file:  提现协议图片
>>>     copyright:  用户协议地址
>>>>        service_url:    炫秘服务协议
>>>>        member_url:     炫秘会员协议
>>>>        private_url:    炫秘用户隐私政策


### 微信方式登录
> wechat/login

> 参数：
>> code: 小程序登录code

> 返回值：
>> id:  会员id
>> access_token:    验证token
>> ip:  客户端ip
>> areas:   客户端位置
>> expires_in:  token有效期
>> has_userinfo:    是否授权读取过微信信息
>> is_merchant: 是否注册代理会员


### 微信用户信息更新
> wechat/update

> method: post

> 参数：
>> userinfo: 小程序获取用户授权得到的用户信息

> 返回值:



### 发送手机验证码
> phone/code

> token验证：   true

> method:   post

> 参数：
>> phone:   手机号码

> 返回值：

### 推荐人信息
> merchant

> 参数：
>> id:  推荐人id

> 返回值：
>> info:
>>>     id
>>>     name:   姓名
>>>     avatar: 头像
>>>     company:    公司

### 代理会员注册
> member/register

> token验证：   true

> method:   post

> 参数：
>> mchid:   上级推荐人id
>> phone:   手机号码
>> code:    验证码
>> industry_id: 二级行业分类id

> 返回值：无


### 首页
> home

> 参数：
>> mchid:   商户id/选填。(不填，如果自己是注册过的代理用户则返回自己的主页信息，如果不是则返回系统设置的默认用户的主页信息)

> 返回值：
>> info:        商户信息
>>>     id:
>>>     name:   姓名
>>>     phone:  手机
>>>     duty:   职务
>>>     wechat: 微信
>>>     company:    公司
>>>     summary:    个人简介
>>>     province:   省
>>>     city:       市
>>>     area:       区
>>>     address:    地址
>>>     gps:        坐标
>>>>        lat:    纬度
>>>>        lng:    经度
>>>     show:       展示一切
>>>     group:  代理级别
>>>>        id:
>>>>        title:
>>>     focused:    是否已关注
>>>     expired:    代理是否到期
>>>     tags:   个性标签
>>>>        id:
>>>>        title:  标签
>>>>        likes:  点赞量
>>>     motto:  个性签名
>> banners:     滚动图数组
>>>     id:
>>>     title:  标题
>>>     cover:  图片
>>>     video:  视频
>> articles:    文章/日志
>> products:    产品展示
>> companies:   个人/企业展示


### 个性标签点赞同
> tag/like

> method: post

> 参数：
>>  tagid:  标签id

> 返回值
>>  count:  点赞数量


### 代理会员级别
> groups

> 参数： 无

> 返回值：
>> list:        级别列表
>>> id:
>>> title:      名称
>>> price:      升级价格
>>> cover:      图标
>>> copyright:  会员权益
>>> description:条款说明
>>> contract:   合同图片


### 支付开通代理会员
> order/proxy/new

> method:   post

> token验证：   true

> 参数：无

> 返回值：
>> pay_params:      小程序支付参数


### 会员基本信息
> member/info

> token验证：   true

> 参数： 无

> 返回值：
>> userinfo:    基本信息
>>>     id:
>>>     nickname:   昵称
>>>     name:       姓名
>>>     avatar:     头像
>>>     gender:     性别
>>>     group:      代理级别
>>>     parent:     上级用户
>>>     company:    公司
>>>     duty:       职务
>>>     phone:      手机
>>>     wechat:     微信
>>>     summary:    个人简介
>>>     province:   省
>>>     city:       市
>>>     area:       区
>>>     address:    详细地址
>>>     school:     学校
>>>     nation:     籍贯
>>>>        province:   省
>>>>        city:       市
>>>>        area:       区
>>>     industry:   行业数组(两个项：第1个主行业，第2个子行业。数组空就是没选择过行业)
>>>     gps:    坐标
>>>>        lat:    纬度
>>>>        lng:    经度
>>>     bank:
>>>>        name:   银行名称
>>>>        no:     银行帐号
>>>>        contact:    银行户名
>>>     alipay: 支付宝账号
>>>     proxy_start_time:   代理时间
>>>     proxy_end_time:     代理时间
>>>     focus_count:        关注人数
>>>     fans_count:         粉丝人数
>>>     balance:            帐户余额
>>>     views:              首页点击量
>>>     cart_count:         购物车数量
>>>     notice_count:       未读通知数量
>>>     expired:            代理是否到期
>>>     qrcode:             个人二维码
>>>     trail:              是否试用用户
>>>     motto:              个性签名
>>>     tags:               个性标签数组
>>>>        id:
>>>>        title:  标签
>>>>        likes:  点赞数量


### 更新会员基本信息
> member/info

> token验证：   true

> method: post

> 参数：
>> id:
>> name:        姓名
>> company:     公司名称
>> gender:      性别, 0=隐藏,1:男,2:女
>> duty:        职务
>> phone:       手机
>> wechat:      微信号
>> summary:     个人/公司简介
>> province:    省
>> city:        市
>> area:        区
>> address:     详细地址
>> lat:         纬度，必须同时设置经度
>> lng:         经度，必须同时设置纬度
>> school:      学校
>> nation_province: 籍贯/省
>> nation_city: 籍贯/市 
>> nation_area: 籍贯/区
>> bank_name:   银行名称
>> bank_no:     银行帐号
>> bank_contact: 银行户名
>> alipay:      支付宝帐号
>> industry_id: 子行业id
>> motto:       个性签名
>> tags:    标签数组
>>>     id:     标签id（新增标签id设为0）
>>>     title:  标签名称

> 返回值： 无


### 读取会员首页展示一切内容
> member/show

> token验证：   true

> 参数： 无

> 返回值：
>> content: 内容


### 更新会员首页展示一切
> member/show

> token验证：   true

> method: post

> 参数：
>>  content:   编辑器内容

> 返回值：无


### 我的轮播图
> member/banner

> token验证：   true

> 参数:无

> 返回值：
>> list:
>>> id:
>>> title:      标题
>>> cover:      图片
>>> video:      视频
>>> redirect:   暂时没有


### 添加我的轮播图
> member/banner

> token验证：   true

> method:   post

> 参数：
>> title:   标题,必填
>> cover:   图片地址,必填
>> video:   视频地址
>> redirect:    备用链接

> 返回值：无

### 修改我的轮播图
> member/banner/{id}

> token验证：   true

> method:   post

> 参数：
>> title:   标题,必填
>> cover:   图片地址,必填
>> video:   视频地址
>> redirect:    备用链接

> 返回值：无

### 删除我的轮播图
> member/banner/{id}

> token验证：   true

> method:   delete

> 参数： 无

> 返回值：无


### 轮播图排序
> member/banner/order

> token验证：   true

> method:   post

> 参数：
>>  items:  数组
>>>     id:     轮播图id
>>>     index:  排序位置


### 申请过的代理
> member/proxy/apply

> token验证： true

> 参数：无

> 返回值：
>>  apply:  申请内容
>>>     group:  申请的级别
>>>>        id:
>>>>        title: 级别名称
>>>>        icon:   级别图标
>>>     status: 申请状态,1=审核中,2:审核通过,5:审核未通过
>>>     remark: 回复内容

>> 返回code=404说明没有提交过审核申请

### 申请代理升级
> member/proxy/apply

> token验证：   true

> method:   post

> 参数：
>>  groupid:    代理级别id
>>  contract:   代理合同图片路径
>>  bank:       银行回执单图片路径

> 返回值：无


### 文章分类
> article/category

> 参数：
>> type:    文章类型(3:文章/日志,4:产品,5:公司展示,6:个人详情,7:企业简介,8:商务合作,9:荣誉,10:宣传片,11:客户案例,12:团队合影,13:名人,14:发展规划)，选填（默认返回文章/日志分类）

> 返回值：
>> list: 列表
>>> id:
>>> title:  名称

### 我的文章管理
> member/article

> token验证：   true

> 参数：
>> type:    文章类型(3:文章/日志,4:产品,5:公司展示,6:个人详情,7:企业简介,8:商务合作,9:荣誉,10:宣传片,11:客户案例,12:团队合影,13:名人,14:发展规划)，选填（默认返回文章/日志）
>> categoryid:  分类id
>> page
>> pagesize

> 返回值：
>> list:    文章列表
>>>     id:
>>>     title:  标题
>>>     cover:  封面
>>>     video:  视频地址
>>>     views:  点击量
>>>     score:  评分
>>>     created:    添加时间
>>>     origin: 产地/type=产品时有
>>>     price:  价格/type=产品时有
>> count
>> pages


### 前台文章列表
> article

> 参数：
>> mchid:   商户id
>> type:    文章类型(3:文章/日志,4:产品,5:公司展示,6:个人详情,7:企业简介,8:商务合作,9:荣誉,10:宣传片,11:客户案例,12:团队合影,13:名人,14:发展规划)，选填（默认返回文章/日志）
>> categoryid:  分类id
>> page
>> pagesize

> 返回值：
>> list:    文章列表
>>>     id:
>>>     title:      标题
>>>     cover:      封面图
>>>     video:      视频地址
>>>     views:      点击量
>>>     summary:    简介
>>>     score:      评分
>>>     created:    添加时间
>>>     origin: 产地/type=产品时有
>>>     price:  价格/type=产品时有
>> count
>> pages

### 我的文章添加
> member/article

> token验证：   true

> method:   post

> 参数：
>>  type:           文章类型(3:文章/日志,4:产品,5:公司展示,6:个人详情,7:企业简介,8:商务合作,9:荣誉,10:宣传片,11:客户案例,12:团队合影,13:名人,14:发展规划)，必填
>>  categoryid:     分类id,type=3时必填
>>  title:          标题
>>  summary:        简介
>>  cover:          封面图
>>  video:          视频地址
>>  content:        内容数组

### 我的文章修改
> member/article/{id}

> token验证：   true

> method:   post

> 参数：
>>  type:           文章类型(3:文章/日志,4:产品,5:公司展示,6:个人详情,7:企业简介,8:商务合作,9:荣誉,10:宣传片,11:客户案例,12:团队合影,13:名人,14:发展规划)，必填
>>  categoryid:     分类id,type=3时必填
>>  title:          标题
>>  summary:        简介
>>  cover:          封面图
>>  video:          视频地址
>>  content:        内容数组

### 我的文章删除
> member/article/{id}

> token验证：   true

> method:   delete

> 参数：无

### 我的文章排序
> member/article/order

> token验证：   true

> method:   post

> 参数：
>>  items
>>>     id:     文章id
>>>     index:  排序位置

### 文章详情
> article/{id}

> 返回值：
>>  article:    文章详情
>>>     id
>>>     title       标题
>>>     cover       封面
>>>     summary     简介
>>>     video       视频地址
>>>     category    分类
>>>     content     内容数组
>>>     views       点击量

### 我的课程管理
> member/course

> token验证：   true

> 参数:
>> page
>> pagesize

> 返回值：
>> list:    课程列表
>>>     id:
>>>     title:          标题
>>>     cover:          封面
>>>     price:          优惠价格
>>>     market_price:   市场价格
>>>     score:          评分
>>>     views:          点击量
>>>     buy_number:     购买量
>>>     status:         状态
>> count
>> pages

### 我的课程添加
> member/course

> token验证：   true

> method:   post

> 参数：
>> title:           标题
>> cover:           封面
>> banners:         滚动图组
>> price:           优惠价格
>> market_price:    市场价格
>> start_time:      优惠时间
>> end_time:        优惠时间
>> content:         课程内容介绍
>> relate_courses:  关联课程
>> content:         内容数组

> 返回值：无

### 我的课程修改
> member/course/{id}

> token验证：   true

> method:   post

> 参数：
>> title:           标题
>> cover:           封面
>> banners:         滚动图组
>> price:           优惠价格
>> market_price:    市场价格
>> start_time:      优惠时间
>> end_time:        优惠时间
>> content:         课程内容介绍
>> relate_courses:  关联课程
>> content:         内容数组

### 我的课程删除
> member/course/{id}

> token验证：   true

> method:   delete

### 我的课程排序
> member/course/order

> token验证：   true

> method:   post

> 参数：
>> items:   数组
>>>     id:     课程id
>>>     index:  课程位置

### 前台课程列表
> course

> 参数：
>> mchid:   商户id
>> page
>> pagesize

> 返回值：
>> list:    课程列表
>>>     id
>>>     title:          标题
>>>     cover:          封面
>>>     banners:        滚动图组
>>>     price:          优惠价格
>>>     market_price:   市场价格
>>>     score:          评分
>>>     views:          点击量
>>>     buy_number:     购买量
>>>     onsale:         是否在优惠期间
>> count
>> pages

### 前台相关课程
> course/{id}/catalog

> 参数：

> 返回值：
>> list:    课程列表
>>>     id
>>>     title:          标题
>>>     cover:          封面
>>>     price:          优惠价格
>>>     market_price:   市场价格
>>>     score:          评分
>>>     views:          点击量
>>>     buy_number:     购买量
>>>     onsale:         是否在优惠期间


### 课程详情
> course/{id}

> 返回值：
>> course:
>>>     id
>>>     title:      标题
>>>     cover:      封面
>>>     banners:    滚动图
>>>     video:      视频
>>>     content：   内容
>>>     price：     优惠价格
>>>     market_price：  市场价格
>>>     start_time：    优惠时间
>>>     end_time：      优惠时间
>>>     onsale：        是否在优惠期
>>>     score：         评分 
>>>     buy_number：    购买量
>>>     views：         点击量


### 我的会议
> member/meeting

> token验证： true

> 参数：
>> categoryid:  分类id
>> page
>> pagesize

> 返回值：
>> list:
>> count
>> pages

### 会议分类
> meeting/category

> 返回值：
>> list:    分类列表
>>>     id:     分类id
>>>     title:  名称

### 添加会议
> member/meeting

> token验证：   true

> method:       post

> 参数：
>>  title:      标题
>>  categoryid: 分类id
>>  cover:      封面
>>  banners:    滚动图组
>>  content:    内容数组
>>  start_time: 会议时间
>>  end_time:   会议时间
>>  province:   省
>>  city:       市
>>  area:       区
>>  address:    详细地点
>>  lat:        纬度
>>  lng:        经度
>>  skus:       规格项
>>>     title:  标题
>>>     price:  价格
>>  sponor_ids:    赞助商id数组 

> 返回值：

### 修改会议
> member/meeting/{id}

> token验证：   true

> method:   post

> 参数：
>>  title:      标题
>>  categoryid: 分类id
>>  cover:      封面
>>  banners:    滚动图组
>>  content:    内容数组
>>  start_time: 会议时间
>>  end_time:   会议时间
>>  province:   省
>>  city:       市
>>  area:       区
>>  address:    详细地点
>>  lat:        纬度
>>  lng:        经度
>>  skus:       规格项
>>>     id:     新添加规格为0
>>>     title:  标题
>>>     price:  价格
>>  sponor_ids:    赞助商id数组 

> 返回值：

### 删除会议
> member/meeting/{id}

> token验证：   true

> method:   delete

### 会议排序
> member/meeting/order

> token验证： true

> method:   post

> 参数：
>> items
>>>     id      会议id
>>>     index   会议位置

### 前台会议列表
> meeting

> 参数：
>> catgegoryid:     分类id
>> page
>> pagesize

> 返回值：
>> list
>>>     id
>>>     title:      标题
>>>     cover:      封面
>>>     start_time: 开始时间
>>>     end_time:   结束时间
>>>     province:   省
>>>     city:       市
>>>     area:       区
>>>     address:    地址
>>>     gps:        坐标
>>>>        lat:    纬度
>>>>        lng:    经度
>>>     price:      价格
>>>     buy_number: 购买量
>>>     views:      点击量
>> count
>> pages

### 会议详情
> meeting/{id}

> 返回值：
>> meeting:
>>>     id:
>>>     title:      标题
>>>     cover       封面
>>>     banners     滚动图
>>>     category    分类
>>>>        id
>>>>        title   分类名称
>>>     start_time  开始时间
>>>     end_time    结束时间
>>>     province    省
>>>     city        市
>>>     area        区
>>>     address     地址
>>>     gps         坐标
>>>     views       点击量
>>>     buy_number  购买量
>>>     score       评分 
>>>     content     内容数组
>>>     skus        规格数组
>>>>        id
>>>>        title:  规格名称
>>>>        price:  价格
>>>     sponors:    赞助商
>>>>        id
>>>>        nickname    昵称
>>>>        name        姓名
>>>>        phone       电话
>>>>        company     公司
>>>>        avatar      头像
>>>>        group       级别
>>>>>           id
>>>>>           title


### 我的关注
> member/focus

> token验证：   true

> 参数：
>>  page
>>  pagesize

> 返回值：
>>  list:   关注列表
>>>     id:     
>>>     nickname:   昵称
>>>     name:       姓名
>>>     avatar:     头像
>>>     phone:      手机
>>>     company:    公司
>>>     expired:    是否到期
>>  count
>>  pages

### 我的粉丝
> member/fans

> token验证：   true

> 参数：
>>  page
>>  pagesize

> 返回值：
>>  list:   粉丝列表
>>>     id:
>>>     nickname：  昵称
>>>     name：      姓名
>>>     avatar：    头像
>>>     phone：     手机
>>>     company：   公司
>>>     expired:    是否到期
>>  count
>>  pages

### 关注/取消关注
> proxy/focus

> token验证：   true

> method:   post

> 参数：
>> mchid:   商户id,必填

> 返回值：无


### 我的团队
> member/group

> 参数： 
>> parentid:    上级用户id,可选(不填则返回自己的下级所有团队成员)
>> page
>> pagesize

> 返回值：
>> list:    列表
>>> id
>>> nickname    昵称
>>> name        姓名
>>> avatar      头像
>>> group       代理级别
>>> childs      下级成员数量
>> count:   数量
>> childs:  团队数量
>> pages:   页数


### 我的访客记录
> member/visit

> token验证：   true

> 参数：
>> page
>> pagesize

> 返回值：
>> list:
>>> id
>>> member      访问会员
>>>>    id:
>>>>    nickname:   昵称
>>>>    name:       姓名
>>>>    phone:      手机
>>>>    company:    公司名称
>>>>    avatar:     头像
>>>>    expired:    是否到期
>>> created     访问时间
>> count
>> pages

### 添加访问记录
> visit

> token验证：   true

> method:   post

> 参数：
>>  type:       访问类型(member=用户主页, coursr=课程, meeting=会议, article=文章)
>>  id:         访问主页为商户id,其它为对应模型id
>>  title:      标题（访问用户主页用用户昵称的首页，其它用对应title）


### 我的通知
> member/notice

> token验证:    true

> 参数：
>> page
>> pagesize

> 返回值：
>>  list:
>>>     id:
>>>     title:      标题
>>>     content:    内容
>>>     readed:     读取时间
>>>     created:    创建时间
>>  count
>>  pages


### 我的战绩
> member/standing

> 参数：

> 返回值：
>>  total:  总战绩
>>  list:
>>>     id:
>>>     icon:       图标
>>>     title:      事件
>>>     money:      金额
>>>     created:    时间
>>  count
>>  pages


### 我的财务记录
> member/bills

> 参数：

> 返回值：
>>  balance:    余额
>>  list:
>>>     id
>>>     icon:       图标
>>>     title:      事件
>>>     type:       类型(add=增加金额，sub=减少金额)
>>>     money:      金额
>>>     created:    时间
>>  count
>>  pages


### 发起提现请求
> member/withdraw

> method: post

> 参数：
>>  money:      提现金额

> 返回值：


### 我的关注列表
> member/focus

> 参数：
>> page
>> pagesize

> 返回值：
>>  list:   关注列表
>>>     id:
>>>     nickname:   昵称
>>>     name:       姓名
>>>     avatar:     头像
>>>     phone:      手机
>>>     company:    公司名称
>>>     has_home:   是否有主页
>>  count:  数量
>>  pages:  页数


### 我的粉丝列表
> member/fans

> 参数：
>>  page
>>  pagesize

> 返回值：
>>  list:   粉丝列表
>>>     id:
>>>     nickname:   昵称
>>>     name:       姓名
>>>     avatar:     头像
>>>     phone:      手机
>>>     company:    公司名称
>>>     has_home:   是否有主页
>>  count:  数量
>>  pages:  页数


### 好友动态
> friend/timeline

> 参数：
>>  memberid:   对应用户的动态/选填，memberid=0或不填就是自己的
>>  page
>>  pagesize

> 返回值：
>>  list: 动态列表
>>>     member: 会员信息
>>>>        id:
>>>>        nickname:   昵称
>>>>        name:       姓名
>>>>        avatar:     头像
>>>>        industry:   行业数组
>>>     id:     动态对应id
>>>     type:   动态类型
>>>     title:  动态标题
>>>     cover:  动态封面图
>>>     video:  视频地址
>>>     content:    动态内容
>>  count
>>  pages

### 好友推荐搜索
> friend/search

> method:   post

> 参数：
>>  type:   类型(默认recommend=推荐/industry=同行/gps=附近/school=同学/nation=同乡)
>>  lat:    type=gps时必填，经纬度
>>  lng:    type=gps时必填，经纬度
>>  industryid: 选填，type=industry,没填按搜索人自己的行业搜索，填了按填写的行业搜索
>>  key:    搜索关键词
>>  page
>>  pagesize

> 返回值：
>>  list:   推荐会员列表
>>>     id:
>>>     nickname:   昵称
>>>     name:       姓名
>>>     avatar:     头像
>>>     company:    公司
>>>     focused:    是否关注过
>>  count
>>  pages


### 消息会员列表
> message/visitor

> 参数：
>>  key:    搜索关键词
>>  page
>>  pagesize

> 返回值：
>> list:    会员数组
>>>     member: 会员信息
>>>>        id:
>>>>        nickname：  昵称
>>>>        name:   姓名
>>>>        avatar: 头像
>>>     content: 最近消息
>>>     unread: 未读消息数量
>>>     updated: 最近消息发送时间
>> count:
>> pages:

### 消息对话列表
> message/{id}

> 参数：
>>  {id}:   会员id
>>  updated:    最近更新时间，默认0。（=0时返回最近的消息，>0返回在这个更新时间后的新消息）

> 返回值：
>>  list:   消息列表
>>>     id: 消息id
>>>     type:   类型(receive=对方发送的消息,send=我发送的消息)
>>>     content:    消息内容
>>>     created:    发送时间
>>  updated:    最近更新时间


### 发送消息
> message/{id}

> method:   post

> 参数：
>>  {id}:   会员id
>>  content:    消息内容

> 返回值：无

### 未读消息数量
> message/unread

> 参数：无

> 返回值：
>>  count   消息数量

### 前台评价列表
> rates

> 参数：
>> model:   对应模型(course=课程, meeting=会议)
>> id:      模型id

> 返回值：
>> list:
>>> id
>>> member:
>>>>    id:
>>>>    nickname:   昵称
>>>>    avatar      头像
>>> score       评分
>>> content     内容
>>> created     评论时间
>> count
>> pages


### 行业分类
> industry

> 参数：无

> 返回值：
> list: 数组
>>  id:
>>  title:  行业名称
>>  childs: 子行业数组
>>>     id:
>>>     title:  行业名称

### 上传接口
> upload

> 参数：
>>  filetype:   上传图片默认空，上传视频用video

> 返回值:
>>  url:    返回的链接地址

### 购物车
> cart

> 参数：无

> 返回值：
>>  list: 数组
>>>     id
>>>     title:      标题
>>>     cover:      封面
>>>     type:       类型 course=课程/meeting=会议
>>>     model_id:   课程/会议id
>>>     price:      价格
>>>     sku_id:     会议sku id
>>>     skus:       会议sku数组
>>>>        id:
>>>>        title:  sku标题
>>>>        price:  sku价格

### 添加购物车
> cart/add

> method: post

> 参数：
>>  type:   添加类型 course=课程/meeting=会议，必填
>>  id:     添加的课程/会议id，必填
>>  skuid:  会议skuid，添加会议时必填

> 返回值：无

### 更新购物车中会议的sku
> cart/{id}/update

> method: post

> 参数：
>>  skuid:  新的会议sku_id

> 返回值：无

### 删除购物车中商品
> cart/remove

> method: delete

> 参数：
>>  ids:    购物车id数组

> 返回值：无


### 提交购物订单
> order

> method:   post

> 参数：
>>  cart_ids:   购物车id数组

> 返回值
>>  orderid: 订单id

### 支付订单
> order/{id}/pay

> method: post

> 参数：
>>  balance:    是否使用余额支付, 0=不使用/1=使用

> 返回值：
>>  小程序支付参数


### 错误代码

> 404:      对象不存在
> 40002:    token验证错误
> 40003:    token过期
> 40004:    商户不存在
> 40005:    非代理会员
> 40006:    openid不存在
> 40007:    请求参数错误
> 40008:    手机号码错误
> 40009:    生成支付码失败

> 会议添加修改
>> 400101:   未添加会议标题
>> 400102:   未添加会议封面图下
>> 400103:   未添加会议内容
>> 400104:   会议时间错误
>> 400105:   会议地点未选择省
>> 400106:   会议地点未选择市
>> 400107:   未填写会议详细地址
>> 400108:   未填写会议购买规格 
>> 400109:   会议保存其它错误，联系我

> 课程添加修改
>> 400201:   未添加课程标题
>> 400202:   未添加课程封面图片
>> 400203:   未添加课程内容

> 文章添加修改
>> 400301:   未设置文章类型
>> 400302:   未添加文章标题
>> 400303:   未添加文章封面图片
>> 400304:   未添加会议内容
>> 400305:   未选择文章分类

> 我的信息修改
>> 400401:   未添加姓名
>> 400402:   手机号码错误
>> 400403:   未选择行业

> 轮播图修改
>> 400501:   未添加标题
>> 400502:   未添加图片

> 订单
>> 400601:  订单已支付过
>> 400602:  订单其它错误，联系我
>> 400603:  用户已经是正式会员，重复注册下单

> 手机验证
>> 400701:  验证码错误
>> 400702:  未填写手机号码
>> 400703:  手机号码已被注册 
>> 400704:  注册未选择行业

> 代理升级申请
>> 400801:  申请级别低于当前自身级别
>> 400802:  有升级申请还在处理中

> 访问记录
>> 400901:  记录类型错误
>> 400902:  记录标题未填写

> 提现
>> 4001001: 提现金额过低或超过自己的余额
>> 4001002: 其它错误，联系我

> 评论
>> 4001101: 评论出错 联系我

> 购物车
>> 4001201: 购物车添加商品类型错误
>> 4001202: 添加的课程/会议不存在或是自己发布的
>> 4001203: 添加了错误的skuid
>> 4001204: 已经购买过的课程/会议

> 消息
>>  4001301:    接收用户不存在
>>  4001302:    发送内容不能为空
<?php
/**
* author: jumper swordwave
* copyright: 泽诚科技
*/

namespace App\Models;

class ApiErrorCode
{
    const NOTFOUND = 404;
    const APPKEY_ERROR = 40001; //appkey error
    const TOKEN_ERROR = 40002;  //token错误
    const TOKEN_EXPIRED = 40003;    //token过期
    const MEMBER_ERROR = 40004; //请求商户不存在
    const MEMBER_GROUP_ERROR = 40005;   //会员无代理权限
    const OPENID_ERROR = 40006; //用户openid不存在
    const PARAM_ERROR = 40007;  //请求参数错误
    const PHONE_ERROR = 40008;  //手机号码错误
    const PREPAY_ERROR = 40009; //生成支付码错误

    const MEETING_TITLE_ERROR = 400101;     //会议标题错误
    const MEETING_COVER_ERROR = 400102;     //会议封面错误
    const MEETING_CONTENT_ERROR = 400103;   //会议内容错误
    const MEETING_TIME_ERROR = 400104;//会议时间错误
    const MEETING_PROVINCE_ERROR = 400105;  //会议地点选择省
    const MEETING_CITY_ERROR = 400106;      //会议地点选择市
    const MEETING_ADDRESS_ERROR = 400107;   //会议地点
    const MEETING_SKU_ERROR = 400108;       //规格项错误
    const MEETING_SAVE_ERROR = 400109;      //会议保存失败

    const COURSE_TITLE_ERROR = 400201;  //课程标题错误
    const COURSE_COVER_ERROR = 400202;  //课程封面错误
    const COURSE_CONTENT_ERROR = 400203;    //课程内容错误
    
    const ARTICLE_TYPE_ID_ERROR = 400301;   //文章类型错误
    const ARTICLE_TITLE_ERROR = 400302;     //文章标题错误
    const ARTICLE_COVER_ERROR = 400303;     //文章封面错误
    const ARTICLE_CONTENT_ERROR = 400304;   //文章内容错误
    const ARTICLE_CATEGORY_ID_ERROR = 400305;   //文章分类错误
    
    const INFO_NAME_ERROR = 400401;     //姓名错误
    const INFO_PHONE_ERROR = 400402;    //电话错误
    const INFO_INDUSTRY_ERROR = 400403; //行业错误

    const BANNER_TITLE_ERROR = 400501;  //标题错误
    const BANNER_COVER_ERROR = 400502;  //封面错误

    const ORDER_PAYED = 400601;     //订单已支付完成
    const ORDER_ERROR = 400602;     //订单错误
    const ORDER_REG_ERROR = 400603; //用户是已注册正式会员
    const ORDER_CART_EMPTY = 400603;    //未选择下单商品
    const ORDER_STORE_ERROR = 400605;   //其它错误，联系管理员

    const PHONE_CODE_ERROR = 400701;    //手机验证码错误
    const PHONE_EMPTY_ERROR = 400702;   //手机号码为空
    const PHONE_EXISTS_ERROR = 400703;  //手机号码已注册 
    const PHONE_INDUSTRY_ERROR = 400704;    //注册未选择行业

    const PROXY_APPLY_GROUP_ERROR = 400801; //代理升级申请级别错误
    const PROXY_APPLY_EXISTS_ERROR = 400802;    //有升级请求还在处理中
    const PROXY_APPLY_CONTRACT_ERROR = 400803;  //没上传代理合同
    const PROXY_APPLY_BANK_ERROR = 400804;  //没上传银行回执单

    const VISIT_TYPE_ERROR = 400901;    //访问记录类型错误
    const VISIT_TITLE_ERROR = 400902;   //访问记录标题错误

    const WITHDRAW_MONEY_ERROR = 4001001;   //提现金额错误
    const WITHDRAW_UNKNOW_ERROR = 4001002;  //提现其它错误

    const RATE_ERROR = 4001101; //评论错误

    const CART_TYPE_ERROR = 4001201;    //购物车添加商品类型错误
    const CART_MODEL_ERROR = 4001202;   //添加的课程/会议不存在或是自己发布的
    const CART_SKU_ERROR = 4001203;     //添加会议sku错误
    const CART_BUYED_ERROR = 4001204;   //已购买过课程/会议
    
    const MESSAGE_MEMBER_ERROR = 4001301;   //消息发送接收人不存在
    const MESSAGE_CONTENT_ERROR = 4001302;  //消息发送内容不能为空
}
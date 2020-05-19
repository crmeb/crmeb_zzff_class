<?php

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use service\UtilService as Util;
use service\JsonService as Json;
use service\FormBuilder as Form;
use think\Url;
use app\admin\model\user\MemberRecord as MemberRecordModel;



class MemberRecord extends AuthController
{
    public function index()
    {

        return $this->fetch();
    }

    public function member_record_list()
    {
        $where = Util::getMore([
            ['page', 1],
            ['limit', 20],
            ['title', ''],
            ['type', ''],
        ]);
        return Json::successlayui(MemberRecordModel::getPurchaseRecordList($where));
    }

}
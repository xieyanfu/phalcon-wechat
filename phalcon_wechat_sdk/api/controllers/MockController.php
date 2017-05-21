<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 16/11/26
 * Time: 下午5:20
 */
use Phalcon\Http\Request;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Query;
use Phalcon\Tag;
use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class MockController extends ControllerBase
{
    public function indexAction()
    {
        echo 'index action';
    }

    //列出可用接口
    public function ListAction()
    {
        $list = Mock::mockList();
        if ($list) {
            $this->reqAndResponse->sendResponsePacket(200, $list);
        } else {
            $this->reqAndResponse->sendResponsePacket(500);
        }
    }

    //获取接口详情
    public function DetailAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $detail = Mock::mockDetailByName($request->getPost('Name'));
            if ($detail) {
                $this->reqAndResponse->sendResponsePacket(200, $detail);
            } else {
                $this->reqAndResponse->sendResponsePacket(500);
            }
        } else {
            $this->reqAndResponse->sendResponsePacket(400);
        }

    }

    //新增mockup接口
    public function AddAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $data['Name'] = trim($request->getPost('Name'));
            $data['GetPara'] = trim($request->getPost('GetPara'));
            $data['PostPara'] = trim($request->getPost('PostPara'));
            $data['Resp'] = trim($request->getPost('Resp'));
            $data['Description'] = trim($request->getPost('Description'));
            $res = Mock::addMock($data);
            switch ($res) {
                case 200:
                    $this->reqAndResponse->sendResponsePacket(200);
                    break;
                case 300:
                    $this->reqAndResponse->sendResponsePacket(400, null, '接口名已经存在!');
                    break;
                case 500:
                    $this->reqAndResponse->sendResponsePacket(500);
                    break;
            }
        } else {
            $this->reqAndResponse->sendResponsePacket(500);
        }
    }

    //设置某个接口的get参数
    public function SetGetParaAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $mock = Mock::findFirst([
                'conditions' => 'Name = :name:',
                'bind' => ['name' => $request->getPost('Name')],
                'hydration' => Resultset::HYDRATE_ARRAYS
            ]);
            $mock->GetPara = $request->getPost('GetPara');
            $mock->UptDt = date('Y-m-d H:i:s');
            $res = $mock->save();
            if ($res) {
                $this->reqAndResponse->sendResponsePacket(200);
            } else {
                $this->reqAndResponse->sendResponsePacket(500);
            }
        }
    }

    //设置某个接口 post参数
    public function SetPostParaAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $mock = Mock::findFirst([
                'conditions' => 'Name = :name:',
                'bind' => ['name' => $request->getPost('Name')],
                'hydration' => Resultset::HYDRATE_ARRAYS
            ]);
            $mock->PostPara = $request->getPost('PostPara');
            $mock->UptDt = date('Y-m-d H:i:s');
            $res = $mock->save();
            if ($res) {
                $this->reqAndResponse->sendResponsePacket(200);
            } else {
                $this->reqAndResponse->sendResponsePacket(500);
            }
        }

    }

    //修改Resp数据
    public function editRespAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $mock = Mock::findFirst([
                'conditions' => 'Name = :name:',
                'bind' => ['name' => $request->getPost('Name')],
                'hydration' => Resultset::HYDRATE_ARRAYS
            ]);
            $mock->Resp = $request->getPost('Resp');
            $mock->UptDt = date('Y-m-d H:i:s');
            $res = $mock->save();
            if ($res) {
                $this->reqAndResponse->sendResponsePacket(200);
            } else {
                $this->reqAndResponse->sendResponsePacket(500);
            }
        }
    }

    //通用修改接口
    public function editParaAction()
    {
        $request = new Request();
        if ($request->isPost()) {
            $mock = Mock::findFirst([
                'conditions' => 'Name = :name:',
                'bind' => ['name' => $request->getPost('Name')],
                'hydration' => Resultset::HYDRATE_ARRAYS
            ]);
            if (!empty($request->getPost('GetPara')) || !empty($request->getPost('PostPara')) || !empty($request->getPost('Resp'))) {
                $mock->GetPara = $request->getPost('GetPara');
                $mock->PostPara = $request->getPost('PostPara');
                $mock->Resp = $request->getPost('Resp');
            }
            $mock->UptDt = date('Y-m-d H:i:s');
            $res = $mock->save();
            if ($res) {
                $this->reqAndResponse->sendResponsePacket(200);
            } else {
                $this->reqAndResponse->sendResponsePacket(500);
            }
        }
    }

    //调用某个接口
    public function CallAction()
    {
        $request = new Request();
        if ($request->isGet()) {
            if ($this->hasName($request->get('Name'))) {
                if ($request->get('Strict') == 0) {
                    $res = $this->getResp($request->get('Name'));
                    $this->reqAndResponse->sendResponsePacket(200, $res);
                } else {
                    $res = $this->checkGetPara($request->get('Name'), $request->get());
                    if ($res) {
                        $res = $this->getResp($request->get('Name'));
                        $this->reqAndResponse->sendResponsePacket(200, $res);
                    } else {
                        $this->reqAndResponse->sendResponsePacket(500);
                    }
                }
            } else {
                $this->reqAndResponse->sendResponsePacket(404, null, '方法名不存在');
            }
        }
        if ($request->isPost()) {
            if ($this->hasName($request->getPost('Name'))) {
                if ($request->getPost('Strict') == 0) {
                    $res = $this->getResp($request->getPost('Name'));
                    $this->reqAndResponse->sendResponsePacket(200, $res);
                } else {
                    $res = $this->checkGetPara($request->getPost('Name'), $request->getPost());
                    if ($res) {
                        $res = $this->getResp($request->getPost('Name'));
                        $this->reqAndResponse->sendResponsePacket(200, $res);
                    } else {
                        $this->reqAndResponse->sendResponsePacket(500);
                    }
                }
            } else {
                $this->reqAndResponse->sendResponsePacket(404, null, '方法名不存在');
            }
        } else {
            $this->reqAndResponse->sendResponsePacket(400, null, '方法名不存在');
        }

    }

    //检查Name是否存在
    public function hasName($name)
    {
        $res = Mock::findFirst([
            'conditions' => 'Name = :name:',
            'bind' => ['name' => $name],
            'hydration' => Resultset::HYDRATE_ARRAYS
        ]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    //直接返回Resp
    public function getResp($name)
    {
        $res = Mock::findFirst([
            'conditions' => 'Name = :name:',
            'bind' => ['name' => $name],
            'columns' => ['Resp'],
            'hydration' => Resultset::HYDRATE_ARRAYS
        ]);
        if ($res) {
            return $res;
        } else {
            return null;
        }
    }

    //检查get参数
    public function checkGetPara($name, $get)
    {
        $getPara = Mock::findFirst([
            'conditions' => 'Name = :name:',
            'bind' => ['name' => $name],
            'columns' => ['GetPara'],
            'hydration' => Resultset::HYDRATE_ARRAYS
        ])->toArray();
        $para = $getPara['GetPara'];
        foreach ($para as $k => $v) {
            if (in_array($v, $get)) {
                return true;
            } else {
                return false;
                exit;
            }
        }
    }


}
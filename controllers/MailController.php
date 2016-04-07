<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Receivemail;
use yii\data\Pagination;


class MailController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionInbox(){
        
        $host = 'imap.qq.com';
        $mail='yangjunalns@qq.com'; 
        $password = 'jxbqtbonmoobbejd';
        $port = '993';
        $array = [];
        $obj= new receiveMail($host,$mail,$password,'imap',$port,true,false);
        

        $obj->connect(); 
        if($obj->is_connected()){
            $pageSize = 10;
            $request = Yii::$app->request;
            $offset = $request->get('offset'); 

            $total = $obj->get_total_emails();  /*总共邮件数量*/
            $unread = $obj->get_unread_emails();    /*未读邮件数量*/

            $currpage = $total-$offset*$pageSize;   /*当前的总量*/
            
            for($i=$currpage;$i>$currpage-$pageSize;$i--){
                $head = $obj->get_email_header($i);
                $array[$i] = $head;
            }
            //分页
            // $pages = new Pagination(['totalCount' =>$total, 'pageSize' => '5']);
            // $model = $array->offset($pages->offset)->limit($pages->limit);
            // echo $obj->listMessages();
            return $this->render('inbox',[
                    'array'=>$array,
                    'total'=>$total,
                    'unread'=>$unread,
                ]);
        }
        $obj->close_mailbox(); 
    }
    public function actionCompose()
    {
        return $this->render('compose');
    }
    public function actionMessage()
    {
        return $this->render('message');
    }
}

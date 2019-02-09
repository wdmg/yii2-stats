<?php

namespace wdmg\stats\behaviors;

use wdmg\stats\models\Visitors;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;
use yii\helpers\Json;

class ControllerBehavior extends \yii\base\Behavior
{

    public function events()
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'onAfterAction'
        ];
    }
    /**
     * @param $event Event
     * @throws \yii\base\Exception
     */
    public function onAfterAction($event)
    {

        $module = Yii::$app->getModule('stats');
        if (($module->ignoreDev && (YII_DEBUG || YII_ENV == 'dev')) || ($module->ignoreAjax && Yii::$app->request->isAjax)) {
            return;
        }

        $request = Yii::$app->request;

        // Ignoring by User IP
        if (in_array($request->userIP, $module->ignoreListIp)) {
            return;
        }

        // Ignoring by User Agent
        if (count($module->ignoreListUA) > 0) {
            foreach($module->ignoreListUA as $user_agent) {

                if(stripos($request->userAgent, $user_agent) !== false)
                    return;

            }
        }

        $cookies = Yii::$app->request->getCookies();

        if (!$cookies->has($module->cookieName)) {
            $cookie = new Cookie();
            $cookie->name = $module->cookieName;
            $cookie->value = Yii::$app->security->generateRandomString();
            $cookie->expire = time() + intval($module->cookieExpire);
            Yii::$app->response->getCookies()->add($cookie);
        } else {
            $cookie = $cookies->get($module->cookieName);
        }

        $visitor = new Visitors();
        $visitor->request_uri = $request->getAbsoluteUrl();
        $visitor->remote_addr = $request->userIP;
        $visitor->remote_host = $request->userHost;
        $visitor->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->id : null;
        $visitor->user_agent = $request->userAgent;
        $visitor->referer_uri = $request->getReferrer();
        $visitor->referer_host = parse_url($request->getReferrer(), PHP_URL_HOST) ? parse_url($request->getReferrer(), PHP_URL_HOST) : null;
        $visitor->https = $request->isSecureConnection ? 1 : 0;
        $visitor->session = $cookie->value;
        $visitor->unique = null; // @TODO: add check behavior
        $visitor->params = Json::encode($request->getQueryParams());
        $visitor->save();

    }

}
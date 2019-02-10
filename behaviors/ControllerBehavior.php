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
        if (count($module->ignoreListIp) > 0) {
            if (in_array($request->userIP, $module->ignoreListIp)) {
                return;
            }
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
        $visitor->remote_addr = $this->getRemoteIp($request);
        $visitor->remote_host = $this->getRemoteHost($request);
        $visitor->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->id : null;
        $visitor->user_agent = $request->userAgent;
        $visitor->referer_uri = $request->getReferrer();
        $visitor->referer_host = parse_url($request->getReferrer(), PHP_URL_HOST) ? parse_url($request->getReferrer(), PHP_URL_HOST) : null;
        $visitor->https = $request->isSecureConnection ? 1 : 0;
        $visitor->type = $this->identityType($request);
        $visitor->session = $cookie->value;
        $visitor->unique = $this->checkUnique($cookie->value);
        $visitor->params = count($request->getQueryParams()) > 0 ? Json::encode($request->getQueryParams()) : null;
        $visitor->save();

    }

    /**
     * Get client IP
     * @param $client_ip string
     */
    public static function getRemoteIp($request)
    {
        $client_ip = $request->userIP;
        if(!$client_ip)
            $client_ip = $request->remoteIP;

        return $client_ip;
    }

    /**
     * Get client HostName
     * @param $host_name string
     */
    public static function getRemoteHost($request)
    {
        $client_ip = self::getRemoteIp($request);

        $host_name = $request->userHost;
        if(!$host_name)
            $host_name = $request->remoteHost;

        if(!$host_name)
            $host_name = gethostbyaddr($client_ip);

        return $host_name;
    }

    /**
     * Is unique visitor
     * @param $session value
     */
    public static function checkUnique($session)
    {
        $count = Visitors::find()->where([
            'session'=> $session
        ])->count();

        if($count > 0)
            return 0;
        else
            return 1;
    }

    /**
     * Determine the type of user
     * @param $request Request
     * @return int
     */
    public static function identityType($request)
    {

        if(preg_match('/(?!&)utm_([a-z0-9=%]+)/i', $request->getReferrer()) || preg_match('/(?!&)utm_([a-z0-9=%]+)/i', $request->getUrl()))
            return Visitors::TYPE_FROM_ADVERTS;
        else if(preg_match('/gclid/i', $request->getReferrer()) || preg_match('/gclid/i', $request->getUrl()))
            return Visitors::TYPE_FROM_ADVERTS;

        if ($request->getReferrer() === null)
            return Visitors::TYPE_DERECT_ENTRY;
        else if (preg_match("($request->hostName)", $request->getReferrer()))
            return Visitors::TYPE_INNER_VISIT;


        if (preg_match("(google|yandex|mail|rambler|yahoo|bing|baidu|aol|ask|duckduckgo)", $request->getReferrer()))
            return Visitors::TYPE_FROM_SEARCH;

        if (preg_match("(facebook|vk|vkontakte|ok|odnoklassniki|instagram|twitter|linkedin|pinterest|tumblr|tumblr|tumblr|flickr|myspace|meetup|tagged|ask.fm|meetme|classmates|loveplanet|badoo|twoo|tinder|lovoo", $request->getReferrer()))
            return Visitors::TYPE_FROM_SOCIALS;

        return Visitors::TYPE_UNDEFINED;
    }

}
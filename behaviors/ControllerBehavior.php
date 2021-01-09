<?php

namespace wdmg\stats\behaviors;

use wdmg\helpers\ArrayHelper;
use wdmg\helpers\StringHelper;
use wdmg\stats\models\Robots;
use wdmg\stats\models\Visitors;
use wdmg\validators\SerialValidator;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;
use yii\helpers\Json;

class ControllerBehavior extends Behavior
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

        // Get stats module
        if (Yii::$app->hasModule('admin/stats'))
            $module = Yii::$app->getModule('admin/stats');
        else
            $module = Yii::$app->getModule('stats');

        // Get stats options
        if (isset(Yii::$app->params['stats.ignoreDev']))
            $ignoreDev = Yii::$app->params['stats.ignoreDev'];
        else
            $ignoreDev = $module->ignoreDev;

        if (isset(Yii::$app->params['stats.ignoreAjax']))
            $ignoreAjax = Yii::$app->params['stats.ignoreAjax'];
        else
            $ignoreAjax = $module->ignoreAjax;

        if (isset(Yii::$app->params['stats.ignoreRoute']))
            $ignoreRoute = Yii::$app->params['stats.ignoreRoute'];
        else
            $ignoreRoute = $module->ignoreRoute;

        if (isset(Yii::$app->params['stats.ignoreListIp']))
            $ignoreListIp = Yii::$app->params['stats.ignoreListIp'];
        else
            $ignoreListIp = $module->ignoreListIp;

        if (isset(Yii::$app->params['stats.ignoreListUA']))
            $ignoreListUA = Yii::$app->params['stats.ignoreListUA'];
        else
            $ignoreListUA = $module->ignoreListUA;

        if (isset(Yii::$app->params['stats.cookieName']))
            $cookieName = Yii::$app->params['stats.cookieName'];
        else
            $cookieName = $module->cookieName;

        if (isset(Yii::$app->params['stats.cookieExpire']))
            $cookieExpire = Yii::$app->params['stats.cookieExpire'];
        else
            $cookieExpire = $module->cookieExpire;

        if (isset(Yii::$app->params['stats.storagePeriod']))
            $storagePeriod = Yii::$app->params['stats.storagePeriod'];
        else
            $storagePeriod = $module->storagePeriod;

        if (isset(Yii::$app->params['stats.detectLocation']))
            $detectLocation = Yii::$app->params['stats.detectLocation'];
        else
            $detectLocation = $module->detectLocation;


        if (($ignoreDev && (YII_DEBUG || YII_ENV == 'dev')) || ($ignoreAjax && Yii::$app->request->isAjax))
            return;

        // Setup GeoIp2 Database reader
        $reader = null;
        if ($detectLocation) {

            $locale = \Locale::getPrimaryLanguage(Yii::$app->language); // Get short locale string
            if (!$locale)
                $locale = 'en';

            try {
                $reader = new \GeoIp2\Database\Reader(__DIR__ .'/../database/GeoLite2-Country.mmdb', [$locale]);
            } catch (Exception $e) {
                Yii::debug($e->getMessage(), __METHOD__);
            }
        }

        // Get request instance
        $request = Yii::$app->request;

        // Ignoring by route
        if (count($ignoreRoute) > 0) {
            foreach ($ignoreRoute as $route) {
                if(preg_match('/('.preg_quote($route,'/').')/i', $request->url) || preg_match('/('.preg_quote($route,'/').')/i', $request->url))
                    return;
            }
        }

        // Ignoring by User IP
        if (count($ignoreListIp) > 0) {
            if (in_array($request->userIP, $ignoreListIp)) {
                return;
            }
        }

        // Ignoring by User Agent
        if (count($ignoreListUA) > 0) {
            foreach($ignoreListUA as $user_agent) {

                if(stripos($request->userAgent, $user_agent) !== false)
                    return;

            }
        }

        $cookies = Yii::$app->request->getCookies();

        if (!$cookies->has($cookieName)) {
            $cookie = new Cookie();
            $cookie->name = $cookieName;
            $cookie->value = Yii::$app->security->generateRandomString();
            $cookie->expire = time() + intval($cookieExpire);
            Yii::$app->response->getCookies()->add($cookie);
        } else {
            $cookie = $cookies->get($cookieName);
        }

        $visitor = new Visitors();
        $visitor->request_uri = $request->getAbsoluteUrl();
        $visitor->remote_addr = $this->getRemoteIp($request);
        $visitor->remote_host = $this->getRemoteHost($request);
        $visitor->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->id : null;
        $visitor->user_agent = $request->userAgent;
        $visitor->referer_uri = $request->getReferrer();
        $visitor->referer_host = $this->getReferrerHost($request);
        $visitor->https = $request->isSecureConnection ? 1 : 0;
        $visitor->type = $this->identityType($request);
        $visitor->code = Yii::$app->response->statusCode;
        $visitor->session = Yii::$app->session->id;
        $visitor->unique = $this->checkUnique($cookie->value);
        $visitor->params = count($request->getQueryParams()) > 0 ? $request->getQueryParams() : null;
        $visitor->robot_id = $this->detectRobot($request->userAgent);

        if ($reader && $detectLocation && $visitor->remote_addr && $visitor->remote_addr !== '127.0.0.1' && $visitor->remote_addr !== '::1') {
            $record = $reader->country($visitor->remote_addr);

            if (isset($record->country->isoCode)) {
                $visitor->iso_code = strtolower($record->country->isoCode);

                $params = [];
                if (!is_array($visitor->params))
                    $params = $visitor->params;

                $params['iso_code'] = strtolower($record->country->isoCode);

                if (isset($record->country->name))
                    $params['country'] = $record->country->name;

            }
            $visitor->params = serialize($params);
        } else {
            $visitor->params = serialize($visitor->params);
        }

        if ($visitor->save())
            $module->setVisitor($visitor);

        if ($storagePeriod !== 0 && rand(1, 10) == 1) {
            $period = (time() - (intval($storagePeriod) * 86400));
            $visitor::clearOldStats($period);
        }
    }

    /**
     * Get referrer hostname
     * @param $request Request
     * @return string or null
     */
    public static function getReferrerHost($request)
    {
        return !empty($request->getReferrer()) ? parse_url($request->getReferrer(), PHP_URL_HOST) : null;
    }

    /**
     * Get client IP
     * @param $request Request
     * @return string or null
     */
    public static function getRemoteIp($request)
    {
        $client_ip = $request->userIP;
        if (!$client_ip)
            $client_ip = $request->remoteIP;

        return rand(195, 200).'.'.rand(120, 195).'.'.rand(150, 250).'.'.rand(1, 250); // For testing only
        //return $client_ip;
    }

    /**
     * Get client hostname
     * @param $request Request
     * @return string or null
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
     * @return integer
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
     * Detect bots
     * @param $user_agent
     * @return integer
     */
    public static function detectRobot($user_agent, $cache_timeout = 3600)
    {
        $db = Robots::getDb();
        $robots = $db->cache(function ($db) {
            return Robots::find()->asArray()->all();
        }, $cache_timeout);

        if (count($robots) > 0) {
            foreach ($robots as $robot) {
                if (!empty($robot["regexp"]) && preg_match("/".preg_quote($robot["regexp"], "/")."/i", $user_agent)) {
                    return $robot["id"];
                }
            }
        }

        return null;
    }

    /**
     * Determine the type of user
     * @param $request Request
     * @return int
     */
    public static function identityType($request)
    {

        // Get stats module
        if (Yii::$app->hasModule('admin/stats'))
            $module = Yii::$app->getModule('admin/stats');
        else
            $module = Yii::$app->getModule('stats');

        if(preg_match('/(?!&)utm_([a-z0-9=%]+)/i', $request->getReferrer()) || preg_match('/(?!&)utm_([a-z0-9=%]+)/i', $request->getUrl()))
            return Visitors::TYPE_FROM_ADVERTS;

        if (count($module->advertisingSystems) > 0) {
            $patterns = implode($module->advertisingSystems, "|");
            if(preg_match('/('.$patterns.')/i', $request->getReferrer()) || preg_match('/('.$patterns.')/i', $request->getUrl()))
                return Visitors::TYPE_FROM_ADVERTS;
        }

        if ($request->getReferrer() === null)
            return Visitors::TYPE_DERECT_ENTRY;
        else if (preg_match("($request->hostName)", $request->getReferrer()))
            return Visitors::TYPE_INNER_VISIT;

        if (count($module->searchEngines) > 0) {
            $patterns = implode($module->searchEngines, "|");
            if(preg_match('/('.$patterns.')/i', $request->getReferrer()))
                return Visitors::TYPE_FROM_SEARCH;
        }

        if (count($module->socialNetworks) > 0) {
            $patterns = implode($module->socialNetworks, "|");
            if(preg_match('/('.$patterns.')/i', $request->getReferrer()))
                return Visitors::TYPE_FROM_SOCIALS;
        }

        return Visitors::TYPE_UNDEFINED;
    }

}
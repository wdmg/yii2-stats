<?php

namespace wdmg\stats\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

use yii\helpers\Console;
use yii\helpers\ArrayHelper;

class InitController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    public function actionIndex($params = null)
    {
        $version = Yii::$app->controller->module->version;
        $welcome =
            '╔════════════════════════════════════════════════╗'. "\n" .
            '║                                                ║'. "\n" .
            '║              STATS MODULE, v.'.$version.'             ║'. "\n" .
            '║          by Alexsander Vyshnyvetskyy           ║'. "\n" .
            '║         (c) 2019 W.D.M.Group, Ukraine          ║'. "\n" .
            '║                                                ║'. "\n" .
            '╚════════════════════════════════════════════════╝';
        echo $name = $this->ansiFormat($welcome . "\n\n", Console::FG_GREEN);
        echo "Select the operation you want to perform:\n";
        echo "  1) Apply all module migrations\n";
        echo "  2) Revert all module migrations\n";
        echo "  3) Update MaxMind GeoIP2 DB\n\n";
        echo "Your choice: ";

        $selected = trim(fgets(STDIN));
        if ($selected == "1") {
            Yii::$app->runAction('migrate/up', ['migrationPath' => '@vendor/wdmg/yii2-stats/migrations', 'interactive' => true]);
        } else if($selected == "2") {
            Yii::$app->runAction('migrate/down', ['migrationPath' => '@vendor/wdmg/yii2-stats/migrations', 'interactive' => true]);
        } else if($selected == "3") {
            exec("curl -sS https://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz > ".__DIR__."/../database/GeoLite2-Country.tar.gz");
            exec("tar -xf ".__DIR__."/../database/GeoLite2-Country.tar.gz -C ".__DIR__."/../database/ --strip-components 1");
            exec("rm ".__DIR__."/../database/GeoLite2-Country.tar.gz");
        } else {
            echo $this->ansiFormat("Error! Your selection has not been recognized.\n\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "\n";
        return ExitCode::OK;
    }
}

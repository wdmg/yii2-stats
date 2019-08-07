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
    public $choice = null;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    public function options($actionID)
    {
        return ['choice', 'color', 'interactive', 'help'];
    }

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

        if(!is_null($this->choice))
            $selected = $this->choice;
        else
            $selected = trim(fgets(STDIN));

        if ($selected == "1") {
            Yii::$app->runAction('migrate/up', ['migrationPath' => '@vendor/wdmg/yii2-stats/migrations', 'interactive' => true]);
        } else if($selected == "2") {
            Yii::$app->runAction('migrate/down', ['migrationPath' => '@vendor/wdmg/yii2-stats/migrations', 'interactive' => true]);
        } else if($selected == "3") {

            $geolitePath = "https://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz";

            $databasePath = __DIR__."/../database/";
            if (!file_exists($databasePath) && !is_dir($databasePath))
                \yii\helpers\FileHelper::createDirectory($databasePath, $mode = 0775, $recursive = true);

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("curl -sS ".$geolitePath." > ".$databasePath."/GeoLite2-Country.tar.gz");
                try {
                    $phar = new \PharData($databasePath."/GeoLite2-Country.tar.gz");
                    if ($phar->extractTo($databasePath, null, true)) {
                        $files = \yii\helpers\FileHelper::findFiles($databasePath, [
                            'only' => ['*.mmdb']
                        ]);
                        foreach($files as $file) {
                            $fileName = pathinfo(\yii\helpers\FileHelper::normalizePath($file), PATHINFO_BASENAME);
                            copy($file, $databasePath.$fileName);
                        }
                    }
                    unlink(__DIR__."/../database/GeoLite2-Country.tar.gz");
                } catch (Exception $e) {
                    echo $name = $this->ansiFormat("Error! " . $e . "\n\n", Console::FG_RED);
                }
            } else {
                exec("curl -sS ".$geolitePath." > ".$databasePath."GeoLite2-Country.tar.gz");
                exec("tar -xf ".$databasePath."GeoLite2-Country.tar.gz -C ".$databasePath." --strip-components 1");
                exec("rm ".$databasePath."GeoLite2-Country.tar.gz");
            }

        } else {
            echo $this->ansiFormat("Error! Your selection has not been recognized.\n\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "\n";
        return ExitCode::OK;
    }
}

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use wdmg\stats\StatsAsset;

/* @var $this yii\web\View */

$this->registerJs(<<< JS

    /* To initialize BS3 tooltips set this below */
    $(function () {
        $("[data-toggle='tooltip']").tooltip(); 
    });
    
    /* To initialize BS3 popovers set this below */
    $(function () {
        $("[data-toggle='popover']").popover(); 
    });

JS
);

$visitorTypes = $model::getVisitorTypeList();
$statusCodes = $model::getStatusCodeList();

?>
<div class="stats-visitors-view stats">
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'request_uri',
            'format' => 'raw',
            'value' => function($data) {
                if ($data->https)
                    return Html::a('<span class="glyphicon glyphicon-lock text-success" data-toggle="tooltip" title="HTTPS"></span>&nbsp;' . $data->request_uri, $data->request_uri, ['target' => "_blank", 'title' => $data->request_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                else
                    return Html::a('<span class="glyphicon glyphicon-lock text-muted" data-toggle="tooltip" title="HTTP"></span>&nbsp;' . $data->request_uri, $data->request_uri, ['target' => "_blank", 'title' => $data->request_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
            },
        ],
        [
            'attribute' => 'code',
            'label' => Yii::t('app/modules/stats', 'Response code'),
            'format' => 'html',
            'value' => function($data) use ($statusCodes) {

                if($data->code !== null && isset($statusCodes[intval($data->code)]))
                    $label = $statusCodes[intval($data->code)];
                else
                    $label = $statusCodes[0];

                if (intval($data->code) == 200)
                    return '<span class="label label-success">'.$data->code.'</span>&nbsp;'.$label;
                elseif (intval($data->code) >= 300 && intval($data->code) < 400)
                    return '<span class="label label-info">'.$data->code.'</span>&nbsp;'.$label;
                elseif (intval($data->code) >= 400 && intval($data->code) < 500)
                    return '<span class="label label-danger">'.$data->code.'</span>&nbsp;'.$label;
                elseif (intval($data->code) >= 500 && intval($data->code) < 600)
                    return '<span class="label label-warning">'.$data->code.'</span>&nbsp;'.$label;
                else
                    return '<span class="label label-default">'.$data->code.'</span>&nbsp;'.$label;
            },
        ],
        [
            'attribute' => 'remote_addr',
            'format' => 'raw',
            'filter' => true,
            'value' => function($data) use ($reader, $module) {
                if (isset($data->remote_addr)) {
                    if (isset($data->iso_code)) {
                        return Html::tag('span', '', [
                            'class' => 'flag flag-'.strtolower($data->iso_code),
                            'data-toggle'=> "tooltip",
                            'title' => ((isset($data->params['country'])) ? trim($data->params['country']) : strtoupper($data->iso_code))
                        ]) . ' ' . Html::a($data->remote_addr, 'https://check-host.net/ip-info?host=' . $data->remote_addr, ['target' => "_blank", 'data-pajax' => 0]);
                    } else if ($module->detectLocation) {
                        try {
                            if ($reader && $data->remote_addr && $data->remote_addr !== '127.0.0.1' && $data->remote_addr !== '::1') {
                                $record = $reader->country($data->remote_addr);
                                return Html::tag('span', '', [
                                        'class' => 'flag flag-'.strtolower($record->country->isoCode),
                                        'data-toggle'=> "tooltip",
                                        'title' => $record->country->name
                                    ]) . ' ' . Html::a($data->remote_addr, 'https://check-host.net/ip-info?host=' . $data->remote_addr, ['target' => "_blank", 'data-pajax' => 0]);
                            } else if ($data->remote_addr) {
                                return $data->remote_addr;
                            }
                        } catch (Exception $e) {
                            if ($data->remote_addr) {
                                return $data->remote_addr;
                            }
                        }
                    } else {
                        return $data->remote_addr;
                    }
                }

                return Yii::t('app/modules/stats', 'Unknow IP');
            },
        ],
        [
            'attribute' => 'remote_host',
            'format' => 'html',
            'visible' => ($model->remote_host) ? true : false,
            'value' => function ($data) {
                return $data->remote_host;
            },
        ],
        [
            'attribute' => 'user_id',
            'format' => 'html',
            'label' => Yii::t('app/modules/stats', 'User'),
            'visible' => ($model->user_id) ? true : false,
            'value' => function($data) {
                if($data->user_id == $data->user['id'])
                    if($data->user['id'] && $data->user['username'])
                        return Html::a($data->user['username'], ['/' . $this->context->module->routePrefix . '/users/view/?id='.$data->user['id']], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    else
                        return $data->user_id;
                else
                    return $data->user_id;
            }
        ],
        [
            'attribute' => 'robot',
            'format' => 'html',
            'visible' => ($model->robot_id) ? true : false,
            'value' => function ($data) {
                $robot = $data->getRobotInfo($data->robot_id);
                if ($robot && count($robot) > 0) {
                    if ($robot->is_badbot) {
                        return '<span class="label label-danger">' . $robot->name . '</span>';
                    } else {
                        return '<span class="label label-default">' . $robot->name . '</span>';
                    }
                } else {
                    return '&nbsp;';
                }
            },
        ],
        [
            'attribute' => 'user_agent',
            'format' => 'html',
            'visible' => ($model->user_agent) ? true : false,
            'contentOptions' => [
                'style' => "word-break:break-all;"
            ],
            'value' => function($data) {
                return Html::encode($data->user_agent);
            },
        ],
        [
            'attribute' => 'referer_uri',
            'format' => 'raw',
            'visible' => ($model->referer_uri) ? true : false,
            'value' => function($data) {
                if ($data->referer_uri)
                    return Html::a($data->referer_uri, $data->referer_uri, ['target' => "_blank", 'title' => $data->referer_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                else
                    return '&nbsp;';
            },
        ],
        [
            'attribute' => 'referer_host',
            'format' => 'html',
            'visible' => ($model->referer_host) ? true : false,
            'value' => function($data) {
                if ($data->referer_host)
                    return '<img src="http://'.$data->referer_host.'/favicon.ico" style="width:18px;margin-right:4px;" /> '.Html::a($data->referer_host, $data->referer_host, ['_target' => "blank"]);
                else
                    return '&nbsp;';
            },
        ],
        [
            'label' => Yii::t('app/modules/stats', 'Client'),
            'format' => 'raw',
            'value' => function($data) use ($clientPlatforms, $clientBrowsers) {
                $client_os = $data->getClientOS($data->user_agent, $clientPlatforms);
                $clinet_browser = $data->getClientBrowser($data->user_agent, $clientBrowsers);
                return '<div><span class="icon '.$client_os['icon'].'" data-toggle="tooltip" title="'.$client_os['title'].'"></span>&nbsp;' .$client_os['title']. '</div><div><span class="icon '.$clinet_browser['icon'].'" data-toggle="tooltip" title="'.$clinet_browser['title'].'"></span>&nbsp;' . $clinet_browser['title'] . '</div>';
            },
        ],
        [
            'attribute' => 'datetime',
            'format' => 'datetime'
        ],
        [
            'attribute' => 'type',
            'label' => Yii::t('app/modules/stats', 'Type of visit'),
            'format' => 'html',
            'value' => function($data) use ($visitorTypes) {
                if ($data->type !== null)
                    return $visitorTypes[$data->type];
                else
                    return $data->type;
            },
        ],
        [
            'attribute' => 'params',
            'format' => 'html',
            'visible' => ($model->params) ? true : false,
            'contentOptions' => [
                'style' => "word-break:break-all;"
            ],
            'value' => function($data) {
                return '<code style="display:inline-block;white-space:normal;">' . Html::encode(var_export($data->params, true)) . '</code>';
            },
        ],
    ]
]); ?>
</div>
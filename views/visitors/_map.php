<?php

use wdmg\stats\StatsAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @let $this yii\web\View */
/* @let $model wdmg\stats\models\VisitorsSearch */

$bundle = StatsAsset::register($this);

?>

<div class="map-wrapper" style="position: static;width:100%;">
    <?= file_get_contents(Url::to($bundle->baseUrl . '/images/world-map.svg', true)); ?>
</div>

<?php

$this->registerJs(<<< JS

    /* To initialize BS3 tooltips set this below */
    $(function () {
        if ($('#world-map path').length > 0) {
            
            let data = $.parseJSON('{$mapData}');
            if (typeof data !== "undefined") {
                let all = parseInt(data['_all']) || 1;
                $.each(data, function (key, val) {
                    let count = parseInt(val);
                    let fill = Math.round(count/all*100);
                    $('#world-map path[data-id="'+key+'"]').data('count', count).addClass('fill-' + Math.ceil(fill/5)*5);
                });
            }
            
            $('#world-map path').each(function () {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let count = $(this).data('count') || 0;
                $(this).tooltip({
                    trigger: 'manual',
                    container: '.map-wrapper',
                    placement: 'top',
                    html: true,
                    title: function() {
                        return name + ': ' + count;
                    },
                });
            });
            
            $('#world-map path').on({
                'mouseenter': function(event) {
                    $(this).tooltip('show');
                },
                'mousemove': function(event) {
                    let tooltip = $(this).attr('aria-describedby');
                    $('#' + tooltip).css({
                        top: event.target.pageY - $('#world-map').offset().top,
                        left: event.target.pageX - $('#world-map').offset().left
                    }).css('display', "inline-block");
                },
                'mouseleave': function(event) {
                    $('#world-map path').tooltip('hide');
                }
            });
            
        }
    });

JS
);

?>
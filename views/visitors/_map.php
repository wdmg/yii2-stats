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
                let tooltip = $(this).tooltip({
                    trigger: 'hover focus',
                    container: '.map-wrapper',
                    placement: 'top',
                    html: true,
                    title: function() {
                        return name + ': ' + count;
                    },
                });
            
                /*$('.map-wrapper').on('mousemove', function(e) {
                    let position = ($(tooltip).attr("data-placement") != "") ? $(tooltip).attr("data-placement") : "right";
                    let top = 0;
                    let left = 0;
                    if (position == "right") {
                        top = +10;
                        left = +10;
                    } else if (position == "left") {
                        top = +10;
                        left = -10;
                    } else if (position == "top") {
                        top = 0;
                        left = 0;
                    } else if (position == "bottom") {
                        top = 20;
                        left = 0;
                    }
                    $(tooltip).css({
                        top: (e.pageY + top),
                        left: (e.pageX + left)
                    });
                    $(tooltip).tooltip('show');
                });
                
                $('.map-wrapper').on('mouseleave', function(e) {
                    $(tooltip).tooltip('hide');
                });*/
            
            });
            
        }
    });

JS
);

?>
<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;


/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', // Google Fonts
        'https://fonts.googleapis.com/css2?family=Monoton&display=swap',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap',
        'https://keshikan.net/fonts-e.html',
        'css/site.css',
        'css/widgets.css',
        'css/spielbericht.css',
        'css/site.css',
        'https://fonts.googleapis.com/css2?family=Material+Icons&display=swap',
        'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
        'https://cdn.jsdelivr.net/npm/awesomplete@1.1.5/awesomplete.min.css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css',
    ];
    public $js = [
        'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js',
        'https://cdn.jsdelivr.net/npm/awesomplete@1.1.5/awesomplete.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}

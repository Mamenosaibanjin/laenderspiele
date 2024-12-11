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
        
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}

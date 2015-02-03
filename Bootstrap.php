<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace neam\yii_workflow_ui_giiant_generator;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package neam\yii_workflow_ui_giiant_generator
 * @author Fredrik Wollsén <fredrik@neam.se>
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {

            if (!isset($app->getModule('gii')->generators['yii-workflow-ui-crud'])) {
                $app->getModule('gii')->generators['yii-workflow-ui-crud'] = 'neam\yii_workflow_ui_giiant_generator\crud\Generator';
            }
            if (!isset($app->getModule('gii')->generators['yii-workflow-ui-codeception-test'])) {
                $app->getModule('gii')->generators['yii-workflow-ui-test'] = 'neam\yii_workflow_ui_giiant_generator\test\Generator';
            }
        }
    }
}
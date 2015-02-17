<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace neam\workflow_ui_giiant_generator;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package neam\workflow_ui_giiant_generator
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

            if (!isset($app->getModule('gii')->generators['workflow-ui-yii1-crud'])) {
                $app->getModule('gii')->generators['workflow-ui-yii1-crud'] = 'neam\workflow_ui_giiant_generator\yii1_crud\Generator';
            }
            if (!isset($app->getModule('gii')->generators['workflow-ui-yii1-tests'])) {
                $app->getModule('gii')->generators['workflow-ui-yii1-test'] = 'neam\workflow_ui_giiant_generator\yii1_test\Generator';
            }
            if (!isset($app->getModule('gii')->generators['workflow-ui-angular-crud-module'])) {
                $app->getModule('gii')->generators['workflow-ui-angular-crud-module'] = 'neam\workflow_ui_giiant_generator\angular_crud_module\Generator';
            }
        }
    }
}
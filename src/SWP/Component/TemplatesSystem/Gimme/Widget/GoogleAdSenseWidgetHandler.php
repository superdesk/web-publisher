<?php
/**
 * Created by PhpStorm.
 * User: sourcefabric
 * Date: 15/06/16
 * Time: 17:28
 */

namespace SWP\Component\TemplatesSystem\Gimme\Widget;


class GoogleAdSenseWidgetHandler extends AbstractWidgetHandler
{
    protected static $expectedParameters = array(
        'ad_unit_type' => [
            'type' => 'string',
            'default' => 'Ad unit'
        ],
        'ad_slot' => [
            'type' => 'int'
        ]
    );

    /**
     * Render widget content
     *
     * @return string
     */
    public function render()
    {
        // Render a template
        return '';
    }
}

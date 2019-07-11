<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use SWP\Bundle\SettingsBundle\Form\Type\BulkSettingsUpdateType;
use SWP\Bundle\SettingsBundle\Form\Type\SettingType;
use SWP\Bundle\SettingsBundle\Controller\SettingsController as BaseController;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends BaseController
{
    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Lists all settings",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"}))
     *         )
     *     )
     * )
     */
    public function listAction(): SingleResourceResponseInterface
    {
        return $this->list();
    }

    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Revert settings to defaults by scope",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     )
     * )
     */
    public function revertAction(string $scope): SingleResourceResponseInterface
    {
        return $this->revert($scope);
    }

    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Change setting value",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=SettingType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Setting not found"
     *     )
     * )
     */
    public function updateAction(Request $request): SingleResourceResponseInterface
    {
        return $this->update($request);
    }

    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Settings bulk update",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=BulkSettingsUpdateType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Setting not found"
     *     )
     * )
     */
    public function bulkAction(Request $request): SingleResourceResponseInterface
    {
        return $this->bulk($request);
    }
}

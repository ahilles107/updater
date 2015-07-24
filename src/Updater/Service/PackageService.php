<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Service;

use Updater\Package\Package;

class PackageService
{
    public function fillPackage($update)
    {
        $package = new Package();
        $package
            ->setVersion($update['version'])
            ->setDescription($update['description'])
            ->setUpdateType($update['update-type'])
            ->setChangelog($update['changelog'])
            ->setMaintainer($update['maintainer'])
            ->setFilemapping($update['filemapping']);

        if (array_key_exists('include', $update)) {
            $package->setInclude($update['include']);
        }

        return $package;
    }
}

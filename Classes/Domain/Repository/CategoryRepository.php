<?php

namespace Tx\CzSimpleCal\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository for Category domain models.
 */
class CategoryRepository extends Repository
{
    /**
     * Returns all categories with the given UIDs.
     *
     * @param array $uids Array of UIDs
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllByUids($uids)
    {
        $query = $this->createQuery();

        $query->matching($query->in('uid', $uids));

        return $query->execute();
    }
}

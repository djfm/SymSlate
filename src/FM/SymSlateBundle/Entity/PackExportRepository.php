<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PackExportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PackExportRepository extends EntityRepository
{
	public function performExport($pack_export_id)
	{
		$export = $this->find($pack_export_id);
	}
}

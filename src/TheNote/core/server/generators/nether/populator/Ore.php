<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\server\generators\nether\populator;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\VectorMath;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OreType;
use TheNote\core\server\generators\normal\populator\Populator;
use function sin;

class Ore extends Populator {

	protected array $oreTypes = [];

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		foreach ($this->oreTypes as $type) {
			$ore = new \pocketmine\world\generator\object\Ore($random, $type);
			for ($i = 0; $i < $ore->type->clusterCount; ++$i) {
				$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
				$y = $random->nextRange($ore->type->minHeight, $ore->type->maxHeight);
				$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
				if ($world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::NETHERRACK())) {
					$this->placeObject($type, $random, $world, $x, $y, $z);
				}
			}
		}
	}

	public function placeObject(OreType $type, Random $random, ChunkManager $world, int $x, int $y, int $z): void {
		$clusterSize = $type->clusterSize;
		$angle = $random->nextFloat() * M_PI;
		$offset = VectorMath::getDirection2D($angle)->multiply($clusterSize / 8);
		$x1 = $x + 8 + $offset->x;
		$x2 = $x + 8 - $offset->x;
		$z1 = $z + 8 + $offset->y;
		$z2 = $z + 8 - $offset->y;
		$y1 = $y + $random->nextBoundedInt(3) + 2;
		$y2 = $y + $random->nextBoundedInt(3) + 2;
		for ($count = 0; $count <= $clusterSize; ++$count) {
			$seedX = $x1 + ($x2 - $x1) * $count / $clusterSize;
			$seedY = $y1 + ($y2 - $y1) * $count / $clusterSize;
			$seedZ = $z1 + ($z2 - $z1) * $count / $clusterSize;
			$size = ((sin($count * (M_PI / $clusterSize)) + 1) * $random->nextFloat() * $clusterSize / 16 + 1) / 2;

			$startX = (int)($seedX - $size);
			$startY = (int)($seedY - $size);
			$startZ = (int)($seedZ - $size);
			$endX = (int)($seedX + $size);
			$endY = (int)($seedY + $size);
			$endZ = (int)($seedZ + $size);

			for ($x = $startX; $x <= $endX; ++$x) {
				$sizeX = ($x + 0.5 - $seedX) / $size;
				$sizeX *= $sizeX;

				if ($sizeX < 1) {
					for ($y = $startY; $y <= $endY; ++$y) {
						$sizeY = ($y + 0.5 - $seedY) / $size;
						$sizeY *= $sizeY;

						if ($y > 0 and ($sizeX + $sizeY) < 1) {
							for ($z = $startZ; $z <= $endZ; ++$z) {
								$sizeZ = ($z + 0.5 - $seedZ) / $size;
								$sizeZ *= $sizeZ;

								if (($sizeX + $sizeY + $sizeZ) < 1 and $world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::NETHERRACK())) {
									$world->setBlockAt($x, $y, $z, $type->material);
								}
							}
						}
					}
				}
			}
		}
	}

	public function setOreTypes(array $types): void {
		$this->oreTypes = $types;
	}
}
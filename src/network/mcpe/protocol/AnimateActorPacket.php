<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

class AnimateActorPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::ANIMATE_ACTOR_PACKET;

	/** @var string */
	public $animation;
	/** @var string */
	public $nextState;
	/** @var string */
	public $stopCondition;
	/** @var string */
	public $controller;
	/** @var float */
	public $blendOutTime;
	/** @var int[] */
	public $entityRuntimeIds = [];

	/**
	 * @param int[] $entityRuntimeIds
	 */
	public static function create(string $animation, string $nextState, string $stopCondition, string $controller, float $blendOutTime, array $entityRuntimeIds = []) : self{
		$result = new self;
		$result->animation = $animation;
		$result->nextState = $nextState;
		$result->stopCondition = $stopCondition;
		$result->controller = $controller;
		$result->blendOutTime = $blendOutTime;
		$result->entityRuntimeIds = $entityRuntimeIds;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->animation = $in->getString();
		$this->nextState = $in->getString();
		$this->stopCondition = $in->getString();
		$this->controller = $in->getString();
		$this->blendOutTime = $in->getFloat();
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$this->entityRuntimeIds[] = $in->getEntityRuntimeId();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->animation);
		$out->putString($this->nextState);
		$out->putString($this->stopCondition);
		$out->putString($this->controller);
		$out->putFloat($this->blendOutTime);
		$out->putUnsignedVarInt(count($this->entityRuntimeIds));
		foreach($this->entityRuntimeIds as $entityRuntimeId){
			$out->putEntityRuntimeId($entityRuntimeId);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAnimateActor($this);
	}
}

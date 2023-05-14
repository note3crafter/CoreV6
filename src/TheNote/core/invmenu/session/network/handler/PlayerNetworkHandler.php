<?php

declare(strict_types=1);

namespace TheNote\core\invmenu\session\network\handler;

use Closure;
use TheNote\core\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
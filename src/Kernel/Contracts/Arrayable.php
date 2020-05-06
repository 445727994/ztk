<?php

/*
 * This file is part of the Yhcztk/zhetaoke.
 *
 * (c) Yhcztk <Yhcztk@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yhcztk\Zhetaoke\Kernel\Contracts;

use ArrayAccess;

interface Arrayable extends ArrayAccess
{
    public function toArray();
}

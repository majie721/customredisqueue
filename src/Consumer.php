<?php

namespace Majie721\CustomRedisQueue;

interface Consumer
{
    public function consume($data);
}

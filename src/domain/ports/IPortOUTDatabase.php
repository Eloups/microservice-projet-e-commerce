<?php

namespace Src\Domain\Ports;

use Src\Domain\Entities\Cart;

interface IPortOUTDatabase {
    public function addArticleToCart(int $cartId, int $articleId): Cart;
}
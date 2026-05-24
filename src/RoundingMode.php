<?php

declare(strict_types=1);

namespace Whallysson\Money;

enum RoundingMode
{
    case HalfAwayFromZero;
    case HalfTowardsZero;
    case HalfEven;
    case TowardsZero;
    case AwayFromZero;
    case NegativeInfinity;
    case PositiveInfinity;
}

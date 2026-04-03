<?php

declare(strict_types=1);

namespace Phpv\Output;

/**
 * Proxy exposing the internal parcel of the validatior.
 * The idea is making it "immutable" so that cosumers cannot mess up with it.
 */
class ValidationParcelProxy
{
    private $parcel;

    public function __construct(ValidationParcel $parcel)
    {
        $this->parcel = $parcel;
    }

    public function isValid(): bool
    {
        return $this->parcel->isValid();
    }

    public function getErrors(): array
    {
        return $this->parcel->getErrors();
    }

    public function numOfErrors(): int
    {
        return $this->parcel->numOfErrors();
    }

    public function getStackedErrorMessages(): array
    {
        return $this->parcel->getStackedErrorMessages();
    }
}

<?php

namespace App\Contracts;

interface HasUniqueNumberInterface
{
    /**
     * Method to return the custom prefix for the unique number.
     *
     * @return string
     */
    public function getNumberPrefix(): string;
}

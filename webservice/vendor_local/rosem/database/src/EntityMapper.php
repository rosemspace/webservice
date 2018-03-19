<?php

namespace Rosem\Database;

interface EntityMapper
{
    public function getFields() : array;

    public function getRelations() : array;
}

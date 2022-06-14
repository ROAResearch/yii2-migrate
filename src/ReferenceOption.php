<?php

namespace roaresearch\yii2\migrate;

enum ReferenceOption: string
{
    case Restrict = 'RESTRICT';
    case Cascade = 'CASCADE';
    case SetNull = 'SET NULL';
    case NoAction = 'NO ACTION';
    case SetDefault = 'SET DEFAULT';
}

<?php

class ParentClass {}

class Children extends ParentClass {}

class SpecificException extends \Exception {}

class AnotherOne extends \RunException {}


namespace a\b\c;

class ParentClass {}

class ChildrenA extends ParentClass {}

class ChildrenB extends \ParentClass {}


namespace d\e\f;

use a\b\c as cAlias;
use a\b\c\ParentClass as ParentClassAlias;

class ParentClass {}

class ChildrenA extends ParentClass {}

class ChildrenB extends \a\b\c\ParentClass {}

class ChildrenC extends cAlias\ParentClass {}

class ChildrenD extends ParentClassAlias {}

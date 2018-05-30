# Entity State

[![Build Status](https://travis-ci.org/Stratadox/EntityState.svg?branch=master)](https://travis-ci.org/Stratadox/EntityState)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/EntityState/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/EntityState?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/EntityState/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/EntityState/?branch=master)
[![Infection Minimum](https://img.shields.io/badge/msi-95-brightgreen.svg)](https://travis-ci.org/Stratadox/EntityState)
[![PhpStan Level](https://img.shields.io/badge/phpstan-7/7-brightgreen.svg)](https://travis-ci.org/Stratadox/EntityState)
[![Maintainability](https://api.codeclimate.com/v1/badges/8c27d62a028e929648d2/maintainability)](https://codeclimate.com/github/Stratadox/EntityState/maintainability)
[![Latest Stable Version](https://poser.pugx.org/stratadox/entity-state/v/stable)](https://packagist.org/packages/stratadox/entity-state)
[![License](https://poser.pugx.org/stratadox/entity-state/license)](https://packagist.org/packages/stratadox/entity-state)

## Installation

Install with `composer require stratadox/entity-state`

## What is this?

This package models the entity state at a certain point in the execution of the 
business logic.

The model is built in such a way that it can find differences in terms of added, 
altered or removed entities, when comparing itself to another point in the process.

Additionally, the package comes with a service to extract the state of the entities.

## How to use this?

Step 1: Extract the state of the entities from the [`Identity Map`](https://github.com/Stratadox/IdentityMap).
```php
$originalState = Extract::state()->from($identityMap);
```
Step 2: Make some changes in the domain.
```php
$identityMap = $identityMap->add('id-26', new Something('foo'));

$identityMap->get(User::class, '1')->changeNameTo('Chuck Norris');

$identityMap = $identityMap->remove(Foo::class, '3');
```
(Note: In real life you'd access the identity map through repositories instead)

Step 3: Extract the new state from the entities in the identity map.
```php
$newState = Extract::state()->from($identityMap);
```
Step 4: Get the changes since the original state was captured.
```php
$changes = $newState->changesSince($originalState);
```
Step 5: Profit.
```php
assert($changes->added()[0]->class() === Something::class);
assert($changes->added()[0]->id() === 'id-26');

assert($changes->altered()[0]->class() === User::class);
assert($changes->altered()[0]->id() === '1');

assert($changes->removed()[0]->class() === Foo::class);
assert($changes->removed()[0]->id() === '3');

assert($changes->altered()[0]->properties()[0]->name() === 'userName');
assert($changes->altered()[0]->properties()[0]->value() === 'Chuck Norris');
```

### Name formatting

Notice that in the above example, the user's name is stored as a string value in
the property `userName`. If instead the `User` class would have a `Name` value 
object, the assertion would look more like this:
```php
assert($changes->altered()[0]->properties()[0]->name() === 'Name:userName.name');
```
If this `Name` object were contained in an array, the result would look like this:
```php
assert($changes->altered()[0]->properties()[0]->name() === 'Name:array:userName[0].name');
```

### Value formatting

It may in some cases be preferable to store the string representation of an 
instance, rather than all its properties.
To extract a string representation of the objects of a class, one can use:
```php
$entityState = Extract::stringifying(UuidInterface::class)->from($identityMap);
```


## To do

Potential to-do's:
- Increase performance by hashing properties names?
- Split names into parts?
- Add methods for querying properties?
- Make properties exportable for hydration

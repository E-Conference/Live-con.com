<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('equipment', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:index',
)));

$collection->add('equipment_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:show',
)));

$collection->add('equipment_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:new',
)));

$collection->add('equipment_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:Equipment:create'),
    array('_method' => 'post')
));

$collection->add('equipment_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:edit',
)));

$collection->add('equipment_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:Equipment:update'),
    array('_method' => 'post|put')
));

$collection->add('equipment_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:Equipment:delete'),
    array('_method' => 'post|delete')
));

return $collection;

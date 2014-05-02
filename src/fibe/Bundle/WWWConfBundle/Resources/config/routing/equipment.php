<?php

  /**
   * @TODO comment
   */

  use Symfony\Component\Routing\Route;
  use Symfony\Component\Routing\RouteCollection;

  $collection = new RouteCollection();

  $collection->add('equipment', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:index',
  )));

  $collection->add('schedule_equipment_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:show',
  )));

  $collection->add('schedule_equipment_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:new',
  )));

  $collection->add('schedule_equipment_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:Equipment:create'),
    array('_method' => 'post')
  ));

  $collection->add('schedule_equipment_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:Equipment:edit',
  )));

  $collection->add('schedule_equipment_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:Equipment:update'),
    array('_method' => 'post|put')
  ));

  $collection->add('schedule_equipment_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:Equipment:delete'),
    array('_method' => 'post|delete')
  ));

  return $collection;

<?php

  /**
   * @TODO comment
   */

  use Symfony\Component\Routing\RouteCollection;
  use Symfony\Component\Routing\Route;

  $collection = new RouteCollection();

  $collection->add('confevent', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:ConfEvent:index',
  )));

  $collection->add('confevent_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:ConfEvent:show',
  )));

  $collection->add('confevent_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:ConfEvent:new',
  )));

  $collection->add('confevent_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:ConfEvent:create'),
    array('_method' => 'post')
  ));

  $collection->add('confevent_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:ConfEvent:edit',
  )));

  $collection->add('confevent_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:ConfEvent:update'),
    array('_method' => 'post|put')
  ));

  $collection->add('confevent_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:ConfEvent:delete'),
    array('_method' => 'post|delete')
  ));

  return $collection;

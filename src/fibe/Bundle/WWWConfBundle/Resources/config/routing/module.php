<?php

  /**
   * @TODO comment
   */

  use Symfony\Component\Routing\RouteCollection;
  use Symfony\Component\Routing\Route;

  $collection = new RouteCollection();

  $collection->add('module', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:Module:index',
  )));

  $collection->add('module_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:Module:show',
  )));

  $collection->add('module_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:Module:new',
  )));

  $collection->add('module_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:Module:create'),
    array('_method' => 'post')
  ));

  $collection->add('module_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:Module:edit',
  )));

  $collection->add('module_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:Module:update'),
    array('_method' => 'post|put')
  ));

  $collection->add('module_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:Module:delete'),
    array('_method' => 'post|delete')
  ));

  return $collection;

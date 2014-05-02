<?php

  /**
   * @TODO comment
   */

  use Symfony\Component\Routing\RouteCollection;
  use Symfony\Component\Routing\Route;

  $collection = new RouteCollection();

  $collection->add('person', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:Person:index',
  )));

  $collection->add('person_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:Person:show',
  )));

  $collection->add('person_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:Person:new',
  )));

  $collection->add('person_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:Person:create'),
    array('_method' => 'post')
  ));

  $collection->add('person_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:Person:edit',
  )));

  $collection->add('person_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:Person:update'),
    array('_method' => 'post|put')
  ));

  $collection->add('person_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:Person:delete'),
    array('_method' => 'post|delete')
  ));

  return $collection;

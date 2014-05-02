<?php

  /**
   * @TODO comment
   */

  use Symfony\Component\Routing\RouteCollection;
  use Symfony\Component\Routing\Route;

  $collection = new RouteCollection();

  $collection->add('paper', new Route('/', array(
    '_controller' => 'fibeWWWConfBundle:Paper:index',
  )));

  $collection->add('paper_show', new Route('/{id}/show', array(
    '_controller' => 'fibeWWWConfBundle:Paper:show',
  )));

  $collection->add('paper_new', new Route('/new', array(
    '_controller' => 'fibeWWWConfBundle:Paper:new',
  )));

  $collection->add('paper_create', new Route(
    '/create',
    array('_controller' => 'fibeWWWConfBundle:Paper:create'),
    array('_method' => 'post')
  ));

  $collection->add('paper_edit', new Route('/{id}/edit', array(
    '_controller' => 'fibeWWWConfBundle:Paper:edit',
  )));

  $collection->add('paper_update', new Route(
    '/{id}/update',
    array('_controller' => 'fibeWWWConfBundle:Paper:update'),
    array('_method' => 'post|put')
  ));

  $collection->add('paper_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'fibeWWWConfBundle:Paper:delete'),
    array('_method' => 'post|delete')
  ));

  return $collection;

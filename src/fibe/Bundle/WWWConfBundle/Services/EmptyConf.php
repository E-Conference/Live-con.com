<?php

namespace fibe\Bundle\WWWConfBundle\Services;

use fibe\Bundle\WWWConfBundle\Entity\Location;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event;

/**
 * Class EmptyConf
 * @package fibe\Bundle\WWWConfBundle\Services
 */
class EmptyConf
{
  /**
   * Empty a conference
   *
   * @param $conference
   * @param $em
   */
  public function emptyConf($conference, $em)
  {
    //  topics
    $topics = $conference->getTopics();
    foreach ($topics as $topic)
    {
      $conference->removeTopic($topic);
      $em->remove($topic);
    }

    //  organizations
    $organizations = $conference->getOrganizations();
    foreach ($organizations as $organization)
    {
      $conference->removeOrganization($organization);
      $em->remove($organization);
    }

    //  topics
    $topics = $conference->getTopics();
    foreach ($topics as $topic)
    {
      $conference->removetopic($topic);
      $em->remove($topic);
    }

    //  papers
    $papers = $conference->getPapers();
    foreach ($papers as $paper)
    {
      $conference->removePaper($paper);
      $em->remove($paper);
    }

    //  locations
    $locations = $conference->getLocations();
    foreach ($locations as $location)
    {
      $conference->removeLocation($location);
      $em->remove($location);
    }

    //  persons
    $persons = $conference->getPersons();
    foreach ($persons as $person)
    {
      $conference->removePerson($person);
      $em->remove($person);
    }

    $mainConfEvent = $conference->getMainConfEvent();

    $newMainConfEvent = new Event();
    $newMainConfEvent->setIsMainConfEvent(true);
    $newMainConfEvent->setSummary("Livecon Conference");
    $newMainConfEvent->setStartAt(new \DateTime('now'));
    $end = new \DateTime('now');
    $newMainConfEvent->setEndAt($end->add(new \DateInterval('P2D')));
    $newMainConfEvent->addCategorie($em->getRepository('fibeWWWConfBundle:Category')->findOneByName("ConferenceEvent"));
    $newMainConfEvent->setConference($conference);
    $conference->setMainConfEvent($newMainConfEvent);

    // conference location
    $mainConfEventLocation = new Location();
    $mainConfEventLocation->setName("Conference's location");
    $newMainConfEvent->setLocation($mainConfEventLocation);
    $mainConfEventLocation->setConference($conference);
    $em->persist($mainConfEventLocation);

    $em->persist($newMainConfEvent);
    $em->remove($mainConfEvent);

    $em->persist($conference);

    //  events
    $events = $conference->getEvents();
    foreach ($events as $event)
    {
      $conference->removeEvent($event);
      $em->remove($event);
    }

    $em->persist($conference);
    $em->flush();
  }

  /**
   * prepare a conference to be deleted
   *
   * @param $conference
   * @param $em
   */
  public function prepareDeleteConf($conference, $em)
  {
    //  topics
    $topics = $conference->getTopics();
    foreach ($topics as $topic)
    {
      $conference->removeTopic($topic);
      $em->remove($topic);
    }

    //  organizations
    $organizations = $conference->getOrganizations();
    foreach ($organizations as $organization)
    {
      $conference->removeOrganization($organization);
      $em->remove($organization);
    }

    //  topics
    $topics = $conference->getTopics();
    foreach ($topics as $topic)
    {
      $conference->removetopic($topic);
      $em->remove($topic);
    }

    //  papers
    $papers = $conference->getPapers();
    foreach ($papers as $paper)
    {
      $conference->removePaper($paper);
      $em->remove($paper);
    }

    //  locations
    $locations = $conference->getLocations();
    foreach ($locations as $location)
    {
      $conference->removeLocation($location);
      $em->remove($location);
    }

    //  events
    $events = $conference->getEvents();
    foreach ($events as $event)
    {
      $conference->removeEvent($event);
      $em->remove($event);
    }

    //  persons
    $persons = $conference->getPersons();
    foreach ($persons as $person)
    {
      $conference->removePerson($person);
      $em->remove($person);
    }

    //authorizations
    $authorizations = $conference->getAuthorizations();
    foreach ($authorizations as $authorization)
    {
      $conference->removeAuthorization($authorization);
      $em->remove($authorization);
    }

    //team
    $team = $conference->getTeam();
    $conference->setTeam(null);

    //Remove link between manager and conference
    $managers = $conference->getConfManagers();
    foreach ($managers as $manager)
    {
      $conference->removeConfManager($manager);
    }

    //main conf event
    $mainConfEvent = $conference->getMainConfEvent();
    if ($mainConfEvent)
    {
      $conference->setMainConfEvent(null);
      $em->flush();
      $em->remove($mainConfEvent);
    }
    $em->flush();
    $em->remove($team);
    $em->flush();

    // $em->persist($conference);
  }

}

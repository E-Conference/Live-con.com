<?php

namespace fibe\Bundle\WWWConfBundle\Services;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event; 

class EmptyConf {

    public function emptyConf($conference,$em)
    {

      //  topics
      $topics = $conference->getTopics();
      foreach ($topics as $topic) {
        $conference->removeTopic($topic);
        $em->remove($topic);
      }

      //  organizations
      $organizations = $conference->getOrganizations();
      foreach ($organizations as $organization) {
        $conference->removeOrganization($organization);
        $em->remove($organization);
      }

      //  topics
      $topics = $conference->getTopics();
      foreach ($topics as $topic) {
        $conference->removetopic($topic);
        $em->remove($topic);
      }

      //  papers
      $papers = $conference->getPapers();
      foreach ($papers as $paper) {
        $conference->removePaper($paper);
        $em->remove($paper);
      }

      //  locations
      $locations = $conference->getLocations();
      foreach ($locations as $location) {
        $conference->removeLocation($location);
        $em->remove($location);
      }

      //  events
      $events = $conference->getEvents();
      foreach ($events as $event) {
        $conference->removeEvent($event);
        $em->remove($event);
      }

      //  persons
      $persons = $conference->getPersons();
      foreach ($persons as $person) {
        $conference->removePerson($person);
        $em->remove($person);
      }

      $mainConfEvent = $conference->getMainConfEvent();

      $newMainConfEvent = new Event();
      $newMainConfEvent->setIsMainConfEvent(true);
      $newMainConfEvent->setSummary("Conference"); 
      $newMainConfEvent->setStartAt( new \DateTime('now'));
      $end = new \DateTime('now');
      $newMainConfEvent->setEndAt( $end->add(new \DateInterval('P2D'))); 
      $newMainConfEvent->addCategorie($em->getRepository('IDCISimpleScheduleBundle:Category')->findOneByName("ConferenceEvent"));
      $newMainConfEvent->setSummary("Conference Event");
      $newMainConfEvent->setConference($conference);
      $conference->setMainConfEvent($newMainConfEvent);
      $em->persist($newMainConfEvent); 
      $em->remove($mainConfEvent);
 
      $em->persist($conference);
      $em->flush();
    }
}
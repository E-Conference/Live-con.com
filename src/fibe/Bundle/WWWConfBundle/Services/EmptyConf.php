<?php

namespace fibe\Bundle\WWWConfBundle\Services;

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

      $em->persist($conference);
      $em->flush();
    }
}
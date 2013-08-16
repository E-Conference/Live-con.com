<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;

/**
 * This entity is based on the specification FOAF.
 *
 * This class define a Person.
 *   @ORM\Table(name="person")
 *   @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PersonRepository")
 * 	
 */
class Person
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


     /**
     * email
     *
     *
     * @ORM\Column(type="string", name="email")
     */
    protected $email;

    /**
     * flag_schedule 
     *	Donne l'autorisation a la personne d'accede a l'application schedule
     *
     * @ORM\Column(type="boolean", name="flag_schedule")
     */
    protected $flag_schedule;

     /**
     * flag_schedule_admin 
     *
     *Donne l'autorisation a la personne d'accede a l'application schedule en tant qu'admin
     * @ORM\Column(type="boolean", name="flag_schedule_admin")
     */
    protected $flag_schedule_admin;


     /**
     * flag_data
     *	Donne l'autorisation a la personne d'accede a l'application data paper 
     *
     * @ORM\Column(type="boolean", name="flag_data")
     */
    protected $flag_data;


     /**
     * flag_data_admin 
     *
     * Donne l'autorisation a la personne d'accede a l'application data paper en tant qu'admin(autorisation de creation de compte)
     * @ORM\Column(type="boolean", name="flag_data_admin")
     */
    protected $flag_data_admin;
    
	
    /**
     * created
     *
     * This property specifies the date and time that the calendar
     * information was created by the calendar user agent in the calendar
     * store.
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $created_at;
	

    /**
     * agent
     *
     * An agent (eg. person, group, software or physical artifact)
     *
     * @ORM\Column(type="string", name="agent")
     */
    protected $agent;

    /**
     * name
     * A name for some thing. Name of the person 
     * @ORM\Column(type="string", name="name")
     */
    protected $name;

    /**
     * title
     *
	* Title (Mr, Mrs, Ms, Dr. etc) 
     *
     * @ORM\Column(type="string", length=255, nullable=true,name="title")
     */
    protected $title;

    /**
     * img
     *
     *  image - An image that can be used to represent some thing (ie. those depictions which are particularly representative of something, eg. one's photo on a homepage). 
     * Status:	testing
     * Domain:	having this property implies being a Person
     * Range : every value of this property is a Image
     *
     * @ORM\Column(type="string", nullable=true, name="img")
     */
    protected $img;

    /**
     * depiction
     *
     * depiction - A depiction of some thing. 
	* Status:	testing
	* Domain:	having this property implies being a Thing
	* Range:	every value of this property is a Image
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="depiction")
     */
     protected $depiction;

    /**
     * familyName
     *. familyName - The family name of some person. 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="familyName")
     */
     protected $familyName;

    /**
     * givenName
     *
     * Given name - The given name of some person. 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="givenName")
     */
     protected $givenName;

    /**
     * based_near
     *
     * Ibased near - A location that something is based near, for some broadly human notion of near.
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="based_near")
     */
     protected $based_near;

    /**
     * knows
     *
     * knows - A person known by this person (indicating some level of reciprocated interaction between the parties). 
     *
     * @ORM\Column(type="string", length=255, nullable=true, name="knows")
     */
     protected $knows;

    /**
     * age
     *
     * age - The age in years of some agent. 
     *
     * @ORM\Column(type="string", length=32, name="age")
     */
    protected $age ;

    /**
     * made
     * 
     * made - Something that was made by this agent. 
     *
     * @ORM\Column(type="string", length=32, name="made")
     */
    protected $made;
	
	 /**
     * primary_topic
     * 
     * primary topic - The primary topic of some page or document. 
     *
     * @ORM\Column(type="string", length=32, name="primary_topic")
     */
    protected $primary_topic;

    /**
     * project
     *
     * Project - A project (a collective endeavour of some kind). 
     *
     *  @ORM\Column(type="string", length=32, name="project")
     */
     protected $project;

    /**
     * organization
     * Organization - An organization
	* @ORM\Column(type="string", length=32, name="organization")
     */
    protected $organization;

    /**
     * group
     *
     * Group - A class of Agents. 
	* @ORM\Column(type="string", length=32, name="_group")
     */
    protected $_group;

    /**
     * member
     *
     * member - Indicates a member of a Group 
     *  @ORM\Column(type="string", length=32, name="member")
     */
     protected $member;

    /**
     * document
     *
     * Document - A document.
	* @ORM\Column(type="string", length=32, name="document")
     */
     protected $document;

    /**
     * image
     *
     * Image - An image. 
	* Status:	testing
	* Properties include:	thumbnail depicts
	* Used with:	thumbnail depiction img
	* Subclass Of	Document
	* The class Image is a sub-class of Document corresponding to those documents which are images.
	* Digital images (such as JPEG, PNG, GIF bitmaps, SVG diagrams etc.) are examples of Image.
	*@ORM\Column(type="string", length=32, name="image")
     */
     protected $image;
	 
	public function getId(){
	 
		return $this->id;
	 
	 }
	 
	public function getCreatedAt(){
	 
		return $this->created_at;
	 
	 }
	 
	
	 
	public function getAgent(){
	 
		return $this->agent;
	 
	 }
	 
	public function getName(){
	 
		return $this->name;
	 
	 }
	 
	 
	public function getTitle(){
	 
		return $this->title;
	 
	 }
	 
	public function getImg(){
	 
		return $this->img;
	 
	 }
	 
	public function getDepiction(){
	 
		return $this->depiction;
	 
	 }
	 
	public function getFamilyName(){
	 
		return $this->familyName;
	 
	 }
	 
	public function getGivenName(){
	 
		return $this->givenName;
	 
	 }
	 
	public function getBasedNear(){
	 
		return $this->based_near;
	 
	 }
	 
	public function getKnows(){
	 
		return $this->knows;
	 
	 }
	 
	public function getAge(){
	 
		return $this->age;
	 
	 }
	 
	public function getMade(){
	 
		return $this->made;
	 
	 }
	 
	public function getPrimaryTopic(){
	 
		return $this->primary_topic;
	 
	 }
	 
	public function getProject(){
	 
		return $this->project;
	 
	 }
	 
	public function getOrganization(){
	 
		return $this->organization;
	 
	 }
	 
	public function getGroup(){
	 
		return $this->_group;
	 
	 }
	 
	public function getMember(){
	 
		return $this->member;
	 
	 }
	 
	public function getDocument(){
	 
		return $this->document;
	 
	 }
	 
	public function getImage(){
	 
		return $this->image;
	 
	 }
	 
	 /**
	 * @param \DateTime $createdAt
     * @return CalendarEntity
	 */
	 public function setCreatedAt($created_at){
	 
		$this->created_at = $created_at;
		return $this;
	 
	 }
	 
	
	 
	public function setAgent($agent){

		$this->agent = $agent;
		return $this;
	 
	 }
	 
	public function setName($name){
	 
		$this->name = $name;
		return $this;
	 
	 }
	 
	 
	public function setTitle($title){
	 
		$this->title = $title;
		return $this;
	 
	 }
	 
	public function setImg($img){
	 
		$this->img = $img;
		return $this;
	 
	 }
	 
	public function setDepiction($depiction){
	 
		$this->depiction = $depiction;
		return $this;
	 
	 }
	 
	public function setFamilyName($familyName){
	 
		$this->familyName = $familyName;
		return $this;
	 
	 }
	 
	public function setGivenName($givenName){
	 
		$this->givenName = $givenName;
		return $this;
	 
	 }
	 
	public function setBasedNear($based_near){
	 
		$this->based_near = $based_near;
		return $this;
	 
	 }
	 
	public function setKnows($knows){
	 
		$this->knows = $knows;
		return $this;
	 
	 }
	 
	public function setAge($age){
	 
		$this->age = $age;
		return $this;
	 
	 }
	 
	public function setMade($made){
	 
		$this->made = $made;
		return $this;
	 
	 }
	 
	public function setPrimaryTopic($primary_topic){
	 
		$this->primary_topic = $primary_topic;
		return $this;
	 
	 
	 }
	 
	public function setProject($project){
	 
		$this->project = $project;
		return $this;
	 
	 }
	 
	public function setOrganization($organization){
	 
		$this->organization = $organization;
		return $this;
	 
	 }
	 
	public function setGroup($group){
	 
		$this->_group = $group;
		return $this;
	 
	 }
	 
	public function setMember($member){
	 
		$this->member = $member;
		return $this;
	 
	 }
	 
	public function setDocument($document){
	 
		$this->document = $document;
		return $this;
	 
	 }
	 
	public function setImage($image){
	 
		$this->image = $image;
		return $this;
	 
	 }
}

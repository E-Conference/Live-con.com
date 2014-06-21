<?php

namespace fibe\Bundle\WWWConfBundle\Controller\REST;

use Symfony\Component\HttpFoundation\Request;
use fibe\Bundle\WWWConfBundle\Entity\Organization;
use fibe\Bundle\WWWConfBundle\Form\OrganizationType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;

/**
 * Organization controller.
 *
 *
 */
class OrganizationRESTController extends FOSRestController
{
  
  /**
   *@Rest\View()
  **/
   public function getOrganizationAction($id){

          $em = $this->getDoctrine()->getManager();
          $organization = $this->get('fibe_security.acl_entity_helper')->getEntityACL('VIEW', 'Organization',$id);
          // $organization = $em->getRepository('fibeWWWConfBundle:Organization')->find($id);
             if(!is_object($organization)){
                throw $this->createNotFoundException();
                }
            return $organization;
    }


    /**
     * Lists all Organization entities.
     * @Rest\View()
     */
    public function getOrganizationsAction(Request $request)
    {
        
        //Authorization Verification conference datas manager
        $user=$this->getUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('fibeWWWConfBundle:Organization')->findAll();
   
        return  $entities;
    }


/**
     * Creates a new note from the submitted data.
     *
     *
     * @Rest\View()
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
  public function postOrganizationAction(Request $request)
  {
      $entity = new Organization();
      $user=$this->getUser();

      $serializer = $this->container->get('jms_serializer');
      $organization = $serializer->deserialize( $request->getContent(), ' fibe\Bundle\WWWConfBundle\Entity\Organization', 'json');

      $form = $this->createForm(new OrganizationType($this->getUser()), $entity);
      $form->bind($request);
  

     if ($form->isValid()) {
         $em = $this->getDoctrine()->getManager();
         $em->persist($organization);
         $em->flush();

         return $this->redirect($this->generateUrl('apiREST_get_organization', array('id' => $organization->getId())));
          // return $this->redirectView(
          //         $this->generateUrl(
          //             'apiREST_get_organization',
          //             array('id' => $organization->getId())
          //             ),
          //         Codes::HTTP_CREATED
          //         );
      }

      return array(
          'form' => $form,
      );
  }
  

  /**
 * Put action
 * @var Request $request
 * @var integer $id Id of the entity
 * @return View|array
 */
public function putOrganizationAction(Request $request, $id)
{
    
    $user=$this->getUser();

    //$serializer = $this->container->get('jms_serializer');
    //$entity = $serializer->deserialize( $request->getContent(), ' fibe\Bundle\WWWConfBundle\Entity\Organization', 'json');
    
    $em = $this->getDoctrine()->getManager();
    
    
    $organization =  $em->getRepository('fibeWWWConfBundle:Organization')->find($id);
    

    $form = $this->createForm(new OrganizationType($this->getUser()), $organization);
    $form->bind($request);

    if($form->isValid()){
        $em = $this->getDoctrine()->getManager();
        $em->persist($form->getData());
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
    
    return array(
          'form' => $form,
      );

}

 /**
 * Put action
 * @var Request $request
 * @var integer $id Id of the entity
 * @return View|array
 */
/*public function putOrganizationAction(Request $request, $id)
{
    
    $user=$this->getUser();

    $serializer = $this->container->get('jms_serializer');
    $entity = $serializer->deserialize( $request->getContent(), ' fibe\Bundle\WWWConfBundle\Entity\Organization', 'json');
    if($entity instanceof Organization == false){
   
        return $this->view(null, Codes::HTTP_NOT_MODIFIED);

     }  

       $em = $this->getDoctrine()->getManager();
       $em->merge($entity);
       $em->flush();
    
   return $this->view(null, Codes::HTTP_NO_CONTENT);

}*/


/**
 * Delete action
 * @var integer $id Id of the entity
 * @return View
 */
public function deleteOrganizationAction($id)
{
    $em = $this->getDoctrine()->getManager();
    $organization =  $em->getRepository('fibeWWWConfBundle:Organization')->find($id);

    $em = $this->getDoctrine()->getManager();
    $em->remove($organization);
    $em->flush();

    return $this->view(null, Codes::HTTP_NO_CONTENT);
}

}
        
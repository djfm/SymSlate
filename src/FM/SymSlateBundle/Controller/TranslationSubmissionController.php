<?php

namespace FM\SymSlateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FM\SymSlateBundle\Entity\TranslationSubmission;
use FM\SymSlateBundle\Form\TranslationSubmissionType;
use Symfony\Component\HttpFoundation\Response;

/**
 * TranslationSubmission controller.
 *
 * @Route("/submissions")
 */
class TranslationSubmissionController extends Controller
{
    /**
     * Lists all TranslationSubmission entities.
     *
     * @Route("/", name="translationsubmission")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a TranslationSubmission entity.
     *
     * @Route("/{id}/show", name="translationsubmission_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationSubmission entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a new TranslationSubmission entity.
     *
     * @Route("/create", name="translationsubmission_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {	
		$request = Request::createFromGlobals();
		
		$user = $this->get("security.context")->getToken()->getUser();
		
		$em = $this->getDoctrine()->getManager();
		
        $classification_id = $request->request->get('classification_id');
        $text = $request->request->get('text');
        $message_id = $request->request->get('message_id');
        $language_id = $request->request->get('language_id');

        $message  = $em->getRepository('FMSymSlateBundle:Message')->findOneById($message_id)->getText();
        $language = $em->getRepository('FMSymSlateBundle:Language')->findOneById($language_id);
        $category = $em->getRepository('FMSymSlateBundle:Classification')->findOneById($classification_id)->getCategory();

        $validation = $this->get('translation_validator')->validate($message, $text, $language, $category);

        if($validation['success'] == true)
        {
            $data = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->submitTranslation(
                $user->getId(),
                $message_id,
                $classification_id,
                $request->request->get('translation_id'),
                $language_id,
                $text,
                !isset($validation['warning_message'])
            );

            if(isset($validation['warning_message']))$data['warning_message'] = $validation['warning_message'];
        }
        else
        {
            $data = $validation;
        }

		
		
		$em->flush();
		
    	$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
        
    }

    /**
     * Displays a form to edit an existing TranslationSubmission entity.
     *
     * @Route("/{id}/edit", name="translationsubmission_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationSubmission entity.');
        }

        $editForm = $this->createForm(new TranslationSubmissionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TranslationSubmission entity.
     *
     * @Route("/{id}/update", name="translationsubmission_update")
     * @Method("POST")
     * @Template("FMSymSlateBundle:TranslationSubmission:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TranslationSubmission entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TranslationSubmissionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('translationsubmission_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a TranslationSubmission entity.
     *
     * @Route("/{id}/delete", name="translationsubmission_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FMSymSlateBundle:TranslationSubmission')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TranslationSubmission entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('translationsubmission'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}

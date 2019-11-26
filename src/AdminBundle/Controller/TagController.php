<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\TagType;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/tag")
 */
class TagController extends Controller
{
    /**
     * @Route("/update/{tag}")
     * @Route("/create", name="admin_tag_create")
     * @Template()
     */
    public function updateAction(Request $request, Tag $tag = null)
    {
        $tag ?: $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'Tag add or updated successfully!');

            return $this->redirectToRoute('admin_admin_data');
        }

        return ['tag' => $tag, 'form' => $form->createView()];
    }
}

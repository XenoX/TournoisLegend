<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var News[] $news */
        $news = $em->getRepository(News::class)->findBy([], ['createdAt' => 'DESC']);

        return ['news' => $news];
    }

    /**
     * @Route("/update/{news}")
     * @Route("/create", name="admin_news_create")
     * @Template()
     */
    public function updateAction(Request $request, News $news = null)
    {
        $news ?: $news = new News();

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (null === $news->getUser()) {
                $news->setUser($this->getUser());
            }

            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'News add or updated successfully!');

            return $this->redirectToRoute('admin_news_index');
        }

        return ['news' => $news, 'form' => $form->createView()];
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TournamentBundle\Entity\RankingParticipant;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Workflow\StreamWorkflow;
use TournamentBundle\Workflow\TournamentWorkflow;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

/**
 * Class AppController
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Tournament[] $nextTournaments */
        $nextTournaments = $em->getRepository(Tournament::class)->findBy(
            ['state' => TournamentWorkflow::STATES_NOT_STARTED, 'activated' => true],
            ['startAt' => 'ASC'],
            3
        );

        /** @var Tournament[] $inProgressTournament */
        $inProgressTournament = $em->getRepository(Tournament::class)->findOneBy(
            ['state' => TournamentWorkflow::STATES_IN_PROGRESS], ['startAt' => 'DESC']
        );

        $stream = null;
        if ($inProgressTournament) {
            $stream = $em->getRepository(Stream::class)->findOneBy(
                ['tournament' => $inProgressTournament, 'main' => true, 'state' => StreamWorkflow::STATE_ACCEPTED]
            );
        }

        /** @var News[] $news */
        $news = $em->getRepository(News::class)->findBy(['activated' => true], ['createdAt' => 'DESC'], 4);
        /** @var RankingParticipant[] $topTenRank */
        $topTenRank = $em->getRepository(RankingParticipant::class)->getTopYearRanking();

        return [
            'nextTournaments' => $nextTournaments,
            'inProgressTournament' => $inProgressTournament,
            'stream' => $stream,
            'news' => $news,
            'topTenRank' => $topTenRank
        ];
    }

    /**
     * @Route("/contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $this->get('app.mail')->send(
                ['email' => $email, 'name' => $email],
                null,
                '[Contact] Nouveau message',
                $form->get('message')->getData()
            );

            $this->addFlash('success', 'alert.success.contact');

            return $this->redirectToRoute('app_app_index');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/imprint")
     * @Template()
     */
    public function imprintAction()
    {
        return [];
    }

    /**
     * @Route("/terms")
     * @Template()
     */
    public function termsAction()
    {
        return [];
    }

    /**
     * @Route("/search")
     * @Method("POST")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $keyword = (string) $request->get('keyword');

        $em = $this->getDoctrine()->getManager();
        $tournaments = $em->getRepository(Tournament::class)->findByName($keyword);
        $users = $em->getRepository(User::class)->findByEmailOrUsername($keyword);
        $teams = $em->getRepository(Team::class)->findByName($keyword);

        if (1 === (count($tournaments) + count($users) + count($teams))) {
            $slugify = $this->get('slugify');
            if (1 === count($tournaments)) {
                return $this->redirectToRoute('tournament_tournament_profile', ['tournament' => $tournaments[0]->getId()]);
            }
            if (1 === count($users)) {
                return $this->redirectToRoute('user_user_profile', ['user' => $users[0]->getId(), 'slug' => $slugify->slugify($users[0]->getName())]);
            }
            if (1 === count($teams)) {
                return $this->redirectToRoute('user_team_profile', ['team' => $teams[0]->getId(), 'slug' => $slugify->slugify($teams[0]->getName())]);
            }
        }

        return [
            'tournaments' => $tournaments,
            'users' => $users,
            'teams' => $teams,
            'keyword' => $keyword,
            'keywordEncoded' => base64_encode($keyword)
        ];
    }

    /**
     * @Route("/search/results/{type}/{keywordEncoded}")
     * @Template()
     */
    public function resultsAction(Request $request, string $type, string $keywordEncoded)
    {
        $keyword = base64_decode($keywordEncoded);
        $em = $this->getDoctrine()->getManager();

        if ('users' === $type) {
            $results = $this->get('knp_paginator')->paginate(
                $em->getRepository(User::class)->search($keyword),
                $request->query->getInt('page', 1),
                16
            );
        } elseif ('teams' === $type) {
            $results = $this->get('knp_paginator')->paginate(
                $em->getRepository(Team::class)->search($keyword),
                $request->query->getInt('page', 1),
                16
            );
        } elseif ('tournaments' === $type) {
            $results = $this->get('knp_paginator')->paginate(
                $em->getRepository(Tournament::class)->search($keyword),
                $request->query->getInt('page', 1),
                16
            );
        } else {
            throw new NotFoundHttpException('Type not found, please contact an administrator');
        }

        return [
            'type' => $type,
            'keyword' => $keyword,
            'results' => $results
        ];
    }
}
